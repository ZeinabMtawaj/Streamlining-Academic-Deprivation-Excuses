<?php

namespace App\Admin\Controllers;

use OpenAdmin\Admin\Controllers\AdminController;
use OpenAdmin\Admin\Form;
use OpenAdmin\Admin\Grid;
use OpenAdmin\Admin\Show;
use \App\Models\AcademicYearAndSemester;

class AcademicYearAndSemesterController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'AcademicYearAndSemester';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new AcademicYearAndSemester());

        $grid->column('id', __('admin.id'));
        $grid->column('academic_year', __('admin.academic_year'));
        $grid->column('semester', __('admin.semester'));
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
        $show = new Show(AcademicYearAndSemester::findOrFail($id));

        $show->field('id', __('admin.id'));
        $show->field('academic_year', __('admin.academic_year'));
        $show->field('semester', __('admin.semester'));
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
        $form = new Form(new AcademicYearAndSemester());

        $form->number('academic_year', __('admin.academic_year'));
        $form->number('semester', __('admin.semester'));

        return $form;
    }
}
