<?php

namespace App\Livewire;

use App\Models\User;
use App\Models\Movimiento;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Builder;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Exportable;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\Footer;
use PowerComponents\LivewirePowerGrid\Header;
use PowerComponents\LivewirePowerGrid\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;
use PowerComponents\LivewirePowerGrid\Traits\WithExport;
use Illuminate\Support\Facades\Blade;
use App\Enums\UserRoles;
use WireUi\Traits\WireUiActions;
use Auth;

use Illuminate\Support\Facades\Auth as FacadesAuth;

final class UsersTable extends PowerGridComponent
{
    use WithExport;
    use WireUiActions;

    public function setUp(): array
    {
        $this->showCheckBox();

        return [
            Exportable::make('usuarios')
                ->striped()
                ->type(Exportable::TYPE_XLS),
            Header::make()->showSearchInput(),
            Footer::make()
                ->showPerPage()
                ->showRecordCount(),
        ];
    }

    public function datasource(): Builder
    {
        /** @var \Illuminate\Database\Eloquent\Builder $query */
        $query = User::query()->with('carreras')
            ->orderBy('rol')
            ->orderBy('username');

        // If current user is a Coordinator, restrict the listing to students
        // that belong to the careers the coordinator manages.
        if (Auth::check() && Auth::user()->es('Coordinador')) {
            $careerIds = Auth::user()->carreras()->pluck('carreras.id')->toArray();

            $query->where('rol', UserRoles::ESTUDIANTE->value)
                ->whereHas('carreras', fn(Builder $q) => $q->whereIn('carreras.id', $careerIds));
        }

        return $query;
    }

    public function relationSearch(): array
    {
        return [
            'carreras' => ['nombre', 'siglas']
        ];
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id')
            ->add('name')
            ->add('email')
            ->add('rol')
            ->add('rol_string', fn(User $user) => $user->rol->value)
            ->add('rol_badge', fn(User $user) => Blade::render("<x-badge color='{$user->rol->color()}' label='{$user->rol->value}' sm />"))
            ->add('username', fn(User $user) => $user->username ?? 'N/A')
            ->add('inscrito')
            ->add('inscrito_icon', fn(User $user) => Blade::render('components.disponible-icon', ['disponible' => $user->inscrito]))
            ->add('carreras', function (User $user) {
                return $user->carreras->pluck('name')->join(', ');
            })
            ->add('carreras_badges', function (User $user) {
                if ($user->carreras->count() > 1) {
                    return Blade::render('components.carrera-badge', ['carreras' => $user->carreras]);
                } else {
                    return Blade::render('components.carrera-badge', ['carrera' => $user->carreras()->first()]);
                }
            })
            ->add('created_at');
    }

    public function columns(): array
    {
        return [
            Column::make('Nombre', 'name')
                ->sortable()
                ->searchable(),

            Column::make('Correo electrónico', 'email')
                ->sortable()
                ->searchable(),

            Column::make('Rol', 'rol_badge', 'rol')
                ->contentClasses('flex justify-center')
                ->sortable()
                ->searchable(),

            Column::make('Usuario', 'username')
                ->contentClasses('flex justify-center')
                ->hidden()
                ->sortable()
                ->searchable(),

            Column::make('Carrera(s)', 'carreras_badges')
                ->contentClasses('flex justify-center')
                ->searchable(),

            Column::make('Inscrito', 'inscrito')
                ->hidden(),

            Column::make('¿Inscrito?', 'inscrito_icon', 'inscrito')
                ->contentClasses('flex text-center justify-items-center justify-center')
                ->sortable()
                ->searchable(),

            Column::action('')
                ->title(Blade::render('<x-icon name="gear" class="w-5 h-5 text-gray-600" />')),
        ];
    }

    public function filters(): array
    {
        $roles = collect(UserRoles::cases())->map(function ($case) {
            return [
                'label' => $case->value,
                'value' => $case->name,
            ];
        })->toArray();

        return [
            Filter::select('rol')
                ->dataSource($roles)
                ->optionLabel('label')
                ->optionValue('value'),
            Filter::boolean('inscrito')
                ->label('Sí', 'No')
        ];
    }

    #[\Livewire\Attributes\On('delete')]
    public function delete($rowId): void
    {
        // Only Administrador and Jefe can delete users
        if (!Auth::user() || !Auth::user()->es(['Administrador', 'Jefe'])) {
            $this->notification()->error('No autorizado', 'Acción permitida solo para administradores o jefes.');
            return;
        }

        $row = User::query()->find($rowId);
        // Si es el usuario actual, no permitir eliminar
        // if ($rowId == Auth::user()->id) {
        //     $this->notification()->error('No permitido', 'No puedes eliminar tu propio usuario.');
        //     return;
        // }
        // Si es el usuario administrador, no permitir eliminar
        // if ($row->rol->value == UserRoles::ADMIN) {
        //     $this->notification()->error('No permitido', 'No puedes eliminar un usuario administrador.');
        //     return;
        // }
        // Si el usuario es estudiante y tiene movimientos asociados, no permitir eliminar
        // $movimientos = Movimiento::where('user_id', $rowId)->get();
        // if ($row->rol->value == UserRoles::ESTUDIANTE and count($movimientos) > 0) {
        //     $this->notification()->error('No permitido', 'No puedes eliminar un usuario con movimientos asociados.');
        //     return;
        // }

        $row->delete();
        $this->notification()->error('Registro eliminado', 'Grupo eliminado correctamente.');

        $this->refresh();
    }

    public function actionsFromView(User $row)
    {
        return view('components.row-actions', [
            'model' => 'users',
            'id' => $row->id
        ]);

        // TODO: Agrega botón para reestablecer contraseña
        // TODO: Agrega botón para inscribir si es estudiante
    }

    /**
     * Abre el modal de cambio de contraseña para un estudiante.
     * Se despacha el evento desde el servidor porque Livewire v3 ya no
     * soporta $emit() en el lado del cliente.
     */
    public function openCoordinatorPasswordModal($userId): void
    {
        $this->dispatch('openCoordinatorPasswordModal', $userId);
    }

    #[\Livewire\Attributes\On('impersonate')]
    public function impersonate($userId)
    {
        if (!Auth::user() || !Auth::user()->es('Administrador')) {
            $this->notification()->error('No autorizado', 'Acción permitida solo para administradores.');
            return;
        }

        $user = User::find($userId);
        if (!$user) {
            $this->notification()->error('Usuario no encontrado', 'El usuario seleccionado no existe.');
            return;
        }

        if ($user->es('Administrador')) {
            $this->notification()->error('No permitido', 'No puedes impersonar a otro administrador.');
            return;
        }

        $adminId = Auth::id();

        FacadesAuth::loginUsingId($user->id);

        // Store the impersonator id after loginUsingId to avoid session regeneration
        // wiping previously set session keys when the guard regenerates the session.
        session(['admin_impersonator_id' => $adminId, 'admin_impersonating' => true]);

        return redirect()->route('dashboard');
    }

    /*
    public function actionRules($row): array
    {
       return [
            // Hide button edit for ID 1
            Rule::button('edit')
                ->when(fn($row) => $row->id === 1)
                ->hide(),
        ];
    }
    */
}
