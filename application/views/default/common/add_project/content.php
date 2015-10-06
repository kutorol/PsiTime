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




<div class="row">
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
                        <tr id="line_project_<?=$val['id_project']?>">
                            <td><?=$i?></td>
                            <td><a href="<?=$val['id_project']?>"><?=$val['title']?></a></td>
                            <td>
                                <a href="" class="btn btn-default"><?=$task_views[8]?></a>
                                <a href="" class="btn btn-info"><?=$task_views[10]?></a>
                                <div onclick="deleteData('task/deleteProject','line_project_', <?=$val['id_project']?>);" class="btn btn-danger"><?=$task_views[9]?> <i class="fa fa-trash-o"></i></div>
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


