<?php

namespace App\Admin\Controllers;

use OpenAdmin\Admin\Controllers\AdminController;
use OpenAdmin\Admin\Form;
use OpenAdmin\Admin\Grid;
use OpenAdmin\Admin\Show;
use \App\Models\AdvisorStudentLink;

class AdvisorStudentLinkController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'AdvisorStudentLink';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new AdvisorStudentLink());

        $grid->column('id', __('admin.id'));
        $grid->column('advisor.username', __('admin.advisor'));
        $grid->column('student.username', __('admin.student'));
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
        $show = new Show(AdvisorStudentLink::findOrFail($id));

        $show->field('id', __('admin.id'));
        $show->field('advisor.username', __('admin.advisor'));
        $show->field('student.username', __('admin.student'));
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
        $form = new Form(new AdvisorStudentLink());

        $roleModel = config('admin.database.roles_model');
        
        $role = $roleModel::where('slug', 'student')->firstOrFail(); 
        $students = $role->studentUsers()->get()->pluck('name', 'id'); 

        $role_advisor = $roleModel::where('slug', 'advisor')->firstOrFail(); 
        $advisors = $role_advisor->advisors()->get()->pluck('name', 'id'); 


       


        $form->select('student_id', __('admin.student'))
            ->options($students)
            ->required();

        $form->select('advisor_id', __('admin.advisor'))
            ->options($advisors)
            ->required();



        return $form;
    }
}
