<?php

namespace App\Admin\Controllers;

use OpenAdmin\Admin\Controllers\AdminController;
use OpenAdmin\Admin\Form;
use OpenAdmin\Admin\Grid;
use OpenAdmin\Admin\Show;
use \App\Models\Course;

class CourseController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Course';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Course());

        $grid->column('id', __('admin.id'));
        $grid->column('course_name', __('admin.name'));
        $grid->column('year_semester.academic_year', __('admin.year'));
        $grid->column('year_semester.semester', __('admin.semester'));
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
        $show = new Show(Course::findOrFail($id));

        $show->field('id', __('admin.id'));
        $show->field('course_name', __('admin.name'));
        $show->field('year_semester.year', __('admin.year'));
        $show->field('year_semester.semester', __('admin.semester'));
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
        $form = new Form(new Course());

        $form->text('course_name', __('admin.name'));
        $form->select('year_semester_id', __('admin.year_semester'))
        ->options(\App\Models\AcademicYearAndSemester::all()->mapWithKeys(function ($item) {
            return [$item->id => __('admin.academic_year') . ' - ' . $item->academic_year . ', ' . __('admin.semester') . ' - ' . $item->semester];
        }))
        ->rules('required');
   

        return $form;
    }
}
