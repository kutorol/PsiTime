<div class="row">
    <div class="container text-center">
        <form action="<?=$startUrl;?>/task/addProject" method="post">
            <div class="col-xs-12 col-sm-6 col-sm-offset-3 col-md-4 col-md-offset-4">
                <div class="input-group col-xs-12">
                    <input type="text" class="form-control col-xs-12" name="nameProject" placeholder="<?=$task_views[1]?>" required>
                    <br><br>
                    <span class="label label-danger error"><?=strip_tags(form_error('nameProject'))?></span>
                </div>

                <div class="input-group col-xs-12 ">
                    <?=$task_views[3]?>
                    <input type="checkbox" id="iAdmin" name="iAdmin" value="yes">
                </div>

                <div class="input-group col-xs-12">
                    <input type="text" class="form-control col-xs-12 ui-autocomplete-input"  id="userAutocomplete" autocomplete="off" role="textbox" aria-autocomplete="list" aria-haspopup="true" name="mainUser" placeholder="<?=$task_views[2]?>" required>
                    <br><br>
                    <span class="label label-danger error" id="autocomplete_error"  data-role="tagsinput" ><?=strip_tags(form_error('mainUser'))?></span>
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
                            <td style="max-width: 50px;"><?=$i?></td>
                            <td class="showHiddenName" >
                                <a id="nameProject_<?=$val['id_project']?>" href="<?=$val['id_project']?>"><?=$val['title']?></a>
                                <input onchange="hideA('nameProject_', <?=$val['id_project']?>);" type='text' id='reName__<?=$val['id_project']?>' class='form-control' value='<?=$val['title']?>'>
                            </td>
                            <td >


                                <div id="groupBtn_<?=$val['id_project']?>">
                                    <a href="" class="btn btn-default"><?=$task_views[8]?></a>
                                    <div class="btn btn-info btnReName" id="reNameSave_<?=$val['id_project']?>" data-id="<?=$val['id_project']?>" onclick="reName('nameProject_', <?=$val['id_project']?>, 'reNameSave_', 0);"><?=$task_views[10]?></div>
                                    <div  id="delete_project_<?=$val['id_project']?>" onclick="deleteData('task/deleteProject','line_project_', <?=$val['id_project']?>, 'groupBtn_');" class="btn btn-danger"><?=$task_views[9]?> <i class="fa fa-trash-o"></i></div>
                                </div>
                                <div id="load_<?=$val['id_project']?>" class="btn btn-danger" style="display: none;"><i class="fa fa-spinner fa-spin"></i></div>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="3">
                                <table class="table table-bordered">
                                    <thead><tr><th><?=$task_views[18]?></th></tr></thead>
                                    <tbody>
                                    <tr>
                                        <td>
                                            <input name="addUsers" type="text" class="addUserProject" data-role="tagsinput" placeholder="<?=$task_views[18]?>">
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


