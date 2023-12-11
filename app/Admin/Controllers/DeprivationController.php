<?php

namespace App\Admin\Controllers;

use OpenAdmin\Admin\Controllers\AdminController;
use OpenAdmin\Admin\Form;
use OpenAdmin\Admin\Grid;
use OpenAdmin\Admin\Show;
use \App\Models\Deprivation;

class DeprivationController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Deprivation';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Deprivation());

        $grid->column('id', __('admin.id'));
        $grid->column('student.username', __('admin.student'));
        $grid->column('course.course_name', __('admin.course'));
        $grid->column('initial_absence_percentage', __('admin.initial_absence_percentage'));
        $grid->column('current_absence_percentage', __('admin.current_absence_percentage'));
        $grid->column('status', __('admin.status'));

        $grid->column('created_at', __('admin.created_at'));
        $grid->column('updated_at', __('admin.updated_at'));

        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(Deprivation::findOrFail($id));

        $show->field('id', __('admin.id'));
        $show->field('student.username', __('admin.student'));
        $show->field('course.course_name', __('admin.course'));
        $show->field('initial_absence_percentage', __('admin.initial_absence_percentage'));
        $show->field('current_absence_percentage', __('admin.current_absence_percentage'));
        $show->field('status', __('admin.status'));

        $show->field('created_at', __('admin.created_at'));
        $show->field('updated_at', __('admin.updated_at'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Deprivation());
        $roleModel = config('admin.database.roles_model');
        $role = $roleModel::where('slug', 'student')->firstOrFail(); // Ensure the 'student' role exists
        $students = $role->studentUsers()->get()->pluck('name', 'id'); // Get students as an array [id => name]

        // Replace the student_id field with a select dropdown
        $form->select('student_id', __('admin.student'))
            ->options($students)
            ->required();


        $form->select('course_id', __('admin.course'))
                ->options(\App\Models\Course::all()->pluck('course_name', 'id'))
                ->rules('required');

        $form->decimal('initial_absence_percentage', __('admin.initial_absence_percentage'));
        $form->decimal('current_absence_percentage', __('admin.current_absence_percentage'));

        return $form;
    }
}
