<?php

namespace App\Admin\Controllers;

use \App\Models\Excuse;
use OpenAdmin\Admin\Form;
use OpenAdmin\Admin\Grid;
use OpenAdmin\Admin\Show;
use Illuminate\Support\Str;
use OpenAdmin\Admin\Controllers\AdminController;

class ExcuseController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Excuse';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Excuse());
        $grid->model()->with('deprivation.student', 'deprivation.course');
        

        $grid->column('id', __('admin.id'));
        $grid->column('excuse_file_path', __('admin.excuse_file'))->display(function ($file_path) {
            $currentId = $this->getAttribute('id');

            if ($file_path) {
                $name_of_file = basename($file_path);

                $url = route('file.download', [
                    'model' => 'excuse', 
                    'folder' => 'excuses', 
                    'id' => $currentId, 
                ]);
                return "<a href='#' onclick='event.preventDefault(); window.location.href=\"{$url}\";'>{$name_of_file}</a>";            }
            return 'No file';
        });


        $grid->column('deprivation.student.user_name', __('admin.deprived_student'))
            ->display(function ($value, $column) {
                return $this->deprivation->student->username ?? 'N/A';
            });
        $grid->column('deprivation.course.course_name', __('admin.course_deprived_from'))
            ->display(function ($value, $column) {
                return $this->deprivation->course->course_name ?? 'N/A';
            });
        $grid->column('advisor_decision', __('admin.advisor_decision'));
        $grid->column('committee_decision', __('admin.committee_decision'));
        $grid->column('final_decision', __('admin.final_decision'));
        $grid->column('rejection_reason_file_path', __('admin.rejection_reason_file'))->display(function ($file_path) {
            $currentId = $this->getAttribute('id');

            if ($file_path) {
                $name_of_file = basename($file_path);

                $url = route('file.download', [
                    'model' => 'excuse', 
                    'folder' => 'rejection reason files', 
                    'id' => $currentId, 
                ]);
                return "<a href='#' onclick='event.preventDefault(); window.location.href=\"{$url}\";'>{$name_of_file}</a>";            }
            return 'No file';
        });
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
        $show = new Show(Excuse::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('excuse_file_path', __('admin.excuse_file'));
        $show->field('deprivation.student.username', __('admin.deprived_student'))
        ->as(function () {
            return $this->deprivation->student->username ?? 'N/A';
        });

   $show->field('deprivation.course.course_name', __('admin.course_deprived_from'))
        ->as(function () {
            return $this->deprivation->course->course_name ?? 'N/A';
        });
        $show->field('advisor_decision', __('admin.advisor_decision'));
        $show->field('committee_decision', __('admin.committee_decision'));
        $show->field('final_decision', __('admin.final_decision'));
        $show->field('rejection_reason_file_path', __('admin.rejection_reason_file'));
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
        $form = new Form(new Excuse());
        $form->file('excuse_file_path', __('admin.excuse_file'))
            ->rules('required')
            ->move('excuses/', date('YmdHis') . '-' . Str::random(7))
            ->disk('private');

        $form->select('deprivation_id', __('admin.deprivation'))
            ->options(\App\Models\Deprivation::all()->mapWithKeys(function ($item) {
                return [$item->id => __('admin.deprived_student') . ' - ' . $item->student->username . ', ' . __('admin.course_deprived_from') . ' - ' . $item->course->course_name];
            }))
            ->rules('required');

        // $form->text('advisor_decision', __('admin.advisor_decision'));
        // $form->text('committee_decision', __('admin.committee_decision'));
        // $form->text('final_decision', __('admin.final_decision'));

        $form->file('rejection_reason_file_path', __('admin.rejection_reason_file'))
            ->move('rejection reason files/', date('YmdHis') . '-' . Str::random(7))
            ->disk('private');

        return $form;
    }
}
