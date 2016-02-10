<?php
use humhub\compat\CActiveForm;
use yii\helpers\Html;
?>
<div id="container">
    <div class="panel panel-default">
        <div class="panel-body">

            <?php $form = CActiveForm::begin(); ?>


            <div class="form-group">
                <?php echo $form->textArea($model, 'message', array('class' => 'form-control autosize', 'placeholder' => 'Describe your video')); ?>
                <?php echo $form->error($model, 'message'); ?>
            </div>
    
      <?php
//        echo \humhub\widgets\AjaxButton::widget([
//            'label' => 'Save',
//            'ajaxOptions' => [
//                'type' => 'POST',
//                'beforeSend' => new yii\web\JsExpression('function(html){  $("#post_input_' . $post->id . '_contenteditable").hide(); showLoader("' . $post->id . '"); }'),
//                'success' => new yii\web\JsExpression('function(html){ $(".wall_' . $post->getUniqueId() . '").replaceWith(html); }'),
//                'url' => $post->content->container->createUrl('/post/post/edit', ['id' => $post->id]),
//            ],
//            'htmlOptions' => [
//                'class' => 'btn btn-default btn-sm btn-comment-submit',
//                'id' => 'post_edit_post_' . $post->id,
 //               'type' => 'submit'
//            ]
//        ]);
        ?>
        
    <?php echo Html::submitButton('Submit', array('class' => 'btn btn-info')); ?>

    <?php
    // Creates Uploading Button
    echo modules\embeddedmedia\widgets\EmbeddedMediaUploadButton::widget(array(
        'uploaderId' => 'post_upload_' . $post->id,
        'object' => $post
    ));
    ?>


    </div>

        <?php
        // Creates a list of already uploaded Files
        echo \modules\embeddedmedia\widgets\EmbeddedMediaUploadList::widget(array(
            'uploaderId' => 'post_upload_' . $post->id,
            'object' => $post
        ));
        ?>

            <?php CActiveForm::end(); ?>
        </div>
    </div>
</div>