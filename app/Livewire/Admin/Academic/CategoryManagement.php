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
    public $name;
    public $min_age;
    public $max_age;
    
    // Campos Grade (Schedule)
    public $selected_category_id;
    public $day_of_week = 'segunda';
    public $start_time;
    public $end_time;
    public $max_capacity = 20;

    public bool $showCategoryDrawer = false;
    public bool $showScheduleDrawer = false;

    public function render()
    {
        $categories = Category::with('schedules')->get();

        return view('livewire.admin.academic.category-management', [
            'categories' => $categories
        ])->layout('layouts.app');
    }

    public function saveCategory()
    {
        $this->validate([
            'name' => 'required|min:3',
            'min_age' => 'nullable|numeric|min:0',
            'max_age' => 'nullable|numeric|gte:min_age',
        ]);

        Category::create([
            'name' => $this->name,
            'min_age' => $this->min_age,
            'max_age' => $this->max_age,
        ]);

        $this->reset(['name', 'min_age', 'max_age', 'showCategoryDrawer']);
        $this->success('Categoria criada com sucesso!');
    }

    public function openScheduleDrawer($categoryId)
    {
        $this->selected_category_id = $categoryId;
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

        Schedule::create([
            'category_id' => $this->selected_category_id,
            'day_of_week' => $this->day_of_week,
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'max_capacity' => $this->max_capacity,
        ]);

        $this->reset(['day_of_week', 'start_time', 'end_time', 'max_capacity', 'showScheduleDrawer']);
        $this->success('Horário adicionado à grade!');
    }

    public function deleteCategory($id)
    {
        Category::findOrFail($id)->delete();
        $this->success('Categoria removida.');
    }

    public function deleteSchedule($id)
    {
        Schedule::findOrFail($id)->delete();
        $this->success('Horário removido.');
    }
}
