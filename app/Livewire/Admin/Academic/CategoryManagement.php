<?php

// filepath: app/Livewire/Admin/Academic/CategoryManagement.php

namespace App\Livewire\Admin\Academic;

use Livewire\Component;
use App\Models\Category;
use App\Models\Schedule;
use Mary\Traits\Toast;

class CategoryManagement extends Component
{
    use Toast;

    // Campos Categoria
    public $category_id;
    public $name;
    public $min_age;
    public $max_age;
    
    // Campos Grade (Schedule)
    public $schedule_id;
    public $selected_category_id;
    public $day_of_week = 'segunda';
    public $start_time;
    public $end_time;
    public $max_capacity = 20;

    public bool $showCategoryDrawer = false;
    public bool $showScheduleDrawer = false;
    public bool $showDeleteModal = false;
    public $idToDelete;
    public $typeToDelete; // 'category' or 'schedule'

    public function render()
    {
        $categories = Category::with('schedules')->get();

        return view('livewire.admin.academic.category-management', [
            'categories' => $categories
        ])->layout('layouts.app');
    }

    public function createCategory()
    {
        $this->reset(['name', 'min_age', 'max_age', 'category_id']);
        $this->showCategoryDrawer = true;
    }

    public function editCategory($id)
    {
        $category = Category::findOrFail($id);
        $this->category_id = $category->id;
        $this->name = $category->name;
        $this->min_age = $category->min_age;
        $this->max_age = $category->max_age;
        
        $this->showCategoryDrawer = true;
    }

    public function saveCategory()
    {
        $this->validate([
            'name' => 'required|min:3',
            'min_age' => 'nullable|numeric|min:0',
            'max_age' => 'nullable|numeric|gte:min_age',
        ]);

        Category::updateOrCreate(
            ['id' => $this->category_id],
            [
                'name' => $this->name,
                'min_age' => $this->min_age,
                'max_age' => $this->max_age,
            ]
        );

        $this->reset(['name', 'min_age', 'max_age', 'showCategoryDrawer', 'category_id']);
        $this->success($this->category_id ? 'Categoria atualizada!' : 'Categoria criada!');
    }

    public function openScheduleDrawer($categoryId)
    {
        $this->reset(['day_of_week', 'start_time', 'end_time', 'max_capacity', 'schedule_id']);
        $this->selected_category_id = $categoryId;
        $this->showScheduleDrawer = true;
    }

    public function editSchedule($id)
    {
        $schedule = Schedule::findOrFail($id);
        $this->schedule_id = $schedule->id;
        $this->selected_category_id = $schedule->category_id;
        $this->day_of_week = $schedule->day_of_week;
        $this->start_time = $schedule->start_time;
        $this->end_time = $schedule->end_time;
        $this->max_capacity = $schedule->max_capacity;
        
        $this->showScheduleDrawer = true;
    }

    public function saveSchedule()
    {
        $this->validate([
            'selected_category_id' => 'required|exists:categories,id',
            'day_of_week' => 'required',
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
            'max_capacity' => 'required|numeric|min:1',
        ]);

        Schedule::updateOrCreate(
            ['id' => $this->schedule_id],
            [
                'category_id' => $this->selected_category_id,
                'day_of_week' => $this->day_of_week,
                'start_time' => $this->start_time,
                'end_time' => $this->end_time,
                'max_capacity' => $this->max_capacity,
            ]
        );

        $this->reset(['day_of_week', 'start_time', 'end_time', 'max_capacity', 'showScheduleDrawer', 'schedule_id']);
        $this->success($this->schedule_id ? 'Horário atualizado!' : 'Horário adicionado!');
    }

    public function confirmDeleteCategory($id)
    {
        $this->idToDelete = $id;
        $this->typeToDelete = 'category';
        $this->showDeleteModal = true;
    }

    public function confirmDeleteSchedule($id)
    {
        $this->idToDelete = $id;
        $this->typeToDelete = 'schedule';
        $this->showDeleteModal = true;
    }

    public function delete()
    {
        if ($this->typeToDelete === 'category') {
            Category::findOrFail($this->idToDelete)->delete();
            $this->success('Categoria removida.');
        } else {
            Schedule::findOrFail($this->idToDelete)->delete();
            $this->success('Horário removido.');
        }

        $this->showDeleteModal = false;
    }
}
