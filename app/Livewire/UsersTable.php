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
use Auth;

final class UsersTable extends PowerGridComponent
{
    use WithExport;

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
        return User::query();
    }

    public function relationSearch(): array
    {
        return [];
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id')
            ->add('name')
            ->add('email')
            ->add('rol', function (User $user) {
                return $user->rol->value;
            })
            ->add('username', fn(User $user) => $user->username ?? 'N/A')
            ->add('inscrito')
            ->add('inscrito_icon', fn(User $user) => Blade::render('components.disponible-icon', ['disponible' => $user->inscrito]))
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

            // Column::make('Rol', 'rol')
            //     ->sortable()
            //     ->searchable(),

            Column::make('Usuario', 'username')
                // ->contentClasses('text-center justify-center')
                ->sortable()
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
        return [
            Filter::boolean('inscrito')
                ->label('Sí', 'No')
        ];
    }

    #[\Livewire\Attributes\On('delete')]
    public function delete($rowId): void
    {
        // Si es el usuario actual, no permitir eliminar
        if ($rowId == Auth::user()->id) {
            $this->notification()->error('No permitido', 'No puedes eliminar tu propio usuario.');
            return;
        }
        // Si es el usuario administrador, no permitir eliminar
        if (Auth::user()->rol->value == UserRoles::ADMIN) {
            $this->notification()->error('No permitido', 'No puedes eliminar un usuario administrador.');
            return;
        }
        // Si el usuario es estudiante y tiene movimientos asociados, no permitir eliminar
        $movimientos = Movimiento::where('user_id', $rowId)->get();
        if (Auth::user()->rol->value == UserRoles::ESTUDIANTE and count($movimientos) > 0) {
            $this->notification()->error('No permitido', 'No puedes eliminar un usuario con movimientos asociados.');
            return;
        }

        // User::query()->find($rowId)->delete();
        $this->notification()->error('Registro eliminado', 'Grupo eliminado correctamente.');

        $this->refresh();
    }

    public function actions(User $row): array
    {
        return [
            Button::add('actions')
                ->bladeComponent('row-actions', [
                    'model' => 'users',
                    'id' => $row->id
                ]),
            // Agrega botón para reestablecer contraseña
            // Agrega botón para inscribir si es estudiante
        ];
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
