<?php
/**
 * Если раньше был нажат чекбокс, то его снова нажмем и закроем инпут
 */
if(isset($iAdminCheck)):?>
    <?php if($iAdminCheck === true):?>
        <script>
            $(function() {
                $("#iAdmin").click();
                $("#userAutocomplete").prop("disabled", true);
            });
        </script>
    <?php endif;?>
<?php endif;?>

<div class="row">
    <div class="container text-center">
        <form action="<?=$startUrl;?>/task/addProject" method="post">
            <div class="col-xs-12 col-sm-6 col-sm-offset-3 col-md-4 col-md-offset-4">
                <div class="input-group col-xs-12">
                    <input type="text" class="form-control col-xs-12" value="<?=set_value("nameProject")?>" name="nameProject" placeholder="<?=$task_views[1]?>" required>
                    <br><br>
                    <span class="label label-danger error"><?=strip_tags(form_error('nameProject'))?></span>
                </div>

                <div class="input-group col-xs-12 ">
                    <?=$task_views[3]?>
                    <input type="checkbox" id="iAdmin" name="iAdmin" value="yes">
                </div>

                <div class="input-group col-xs-12">

                    <span class="label label-danger error" id="autocomplete_error"  data-role="tagsinput" ><?=strip_tags(form_error('mainUser'))?></span>
                    <br><br>
                    <input type="text" class="form-control autocomplete="off" col-xs-12 ui-autocomplete-input"  id="userAutocomplete" value="<?=set_value("mainUser")?>" autocomplete="off" role="textbox" aria-autocomplete="list" aria-haspopup="true" name="mainUser" placeholder="<?=$task_views[2]?>" required>
                    </div>

                <div class="input-group col-xs-12">
                    <button type="submit" class="btn btn-primary col-xs-12" name="addProject_btn"><?=$task_views[0]?></button>
                </div>

                <div class="row">
                    <a href="<?=$startUrl;?>/task" class="pull-left"><?=$header_menu_lang[0][$segment]?></a>
                </div>

            </div>

        </form>
    </div>
</div>






<div class="row" id="addProject">
    <div class="container-fluid">
        <div class="col-xs-12 text-center">

            <?php if(!empty($myProjects)):?>
                <h3><?=$task_views[7]?></h3>

                <table class="table table-bordered">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th><?=$task_views[11]?></th>
                        <th><?=$task_views[12]?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php $i = 1; foreach($myProjects as $key=>$val):?>
                        <tr id="line_project_<?=$val['id_project']?>" class="project">
                            <td><?=$i?></td>
                            <td class="showHiddenName" >
                                <a id="nameProject_<?=$val['id_project']?>" href="<?=$val['id_project']?>"><?=$val['title']?></a>
                                <input type='text' id='reName__<?=$val['id_project']?>' class='form-control' value='<?=$val['title']?>'>
                            </td>
                            <td >


                                <div id="groupBtn_<?=$val['id_project']?>" data-id="<?=$val['id_project']?>">
                                    <div class="btn btn-default toogle-user" ><?=$task_views[8]?></div>
                                    <div class="btn btn-info btnReName" id="reNameOpen_<?=$val['id_project']?>" data-id="<?=$val['id_project']?>"><?=$task_views[10]?></div>
                                    <div  id="delete_project_<?=$val['id_project']?>" onclick="deleteData('task/deleteProject','line_project_', <?=$val['id_project']?>);" class="btn btn-danger"><?=$task_views[9]?> <i class="fa fa-trash-o"></i></div>
                                </div>
                                <div class="btn btn-info btnReName" id="reNameSave_<?=$val['id_project']?>" style="display: none;" data-id="<?=$val['id_project']?>"><?=$task_views[17]?></div>
                                <div id="load_<?=$val['id_project']?>" class="btn btn-danger" my-toggle="toggle" style="display: none;"><i class="fa fa-spinner fa-spin"></i></div>
                            </td>
                        </tr>
                        <tr style="background-color: rgb(206, 206, 206);">
                            <td colspan="3">
                                <table class="table table-bordered hidden_my" style="display: none;" id="addUserProject_<?=$val['id_project']?>">
                                    <thead><tr><th><?=$task_views[18]?></th></tr></thead>
                                    <tbody>
                                    <tr>
                                        <td>
                                            <span class="label label-danger error" id="tagsInputAutocompleteError_<?=$val['id_project']?>"></span>
                                            <br><br>
                                            <input name="addUsers" autocomplete="off" data-id="<?=$val['id_project']?>" id="tagsInputAutocomplete_<?=$val['id_project']?>" type="text" class="addUserProject" data-role="tagsinput" placeholder="<?=$task_views[18]?>">
                                            <input type="submit" value="<?=$task_views[25]?>" onclick="attachUsers(<?=$val['id_project']?>);" >
                                            <input type="submit" value="<?=$js[21]?>" onclick="delUserProject(<?=$val['id_project']?>);" >
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                    <?php $i++; endforeach;?>
                    </tbody>
                </table>

            <?php endif;?>

        </div>
    </div>
</div>

<p>&nbsp;</p>


