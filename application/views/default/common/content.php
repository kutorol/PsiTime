<hr>

<?php
/**
 * Если прикреплен к проекту
 * If attached to the project
 */
if($statusUser == 1):?>

<div class="row">
    <div class="container">
        <div>
            <form action="">
                <div class="row">

                    <div class="col-xs-4">
                        <div class="text-label">Задача</div>
                        <div class="input-group col-xs-12">
                            <input type="text" class="form-control col-xs-12" placeholder="Задача">
                        </div>
                    </div>


                    <div class="col-xs-4">
                        <div class="text-label">Сложность</div>
                        <div>
                            <select class="select-hard">
                                <option class="text-light">Легко</option>
                                <option class="text-middle">Средне</option>
                                <option class="text-hard">Трудно</option>
                            </select>
                        </div>
                    </div>

                </div>

                <button type="submit" class="btn btn-primary col-xs-3">Добавить</button>

            </form>
        </div>
    </div>
</div>

<div class="row table-task">
    <div class="container">
        <div class="div">
            <span class="label label-default">Выполнено</span>
            <span class="label label-default">3 задачи</span>
            <span class="label label-default">резерв времени</span>
        </div>

        <div class="table-task">
            <table>
                <thead>
                <tr>
                    <td>Задача</td>
                    <td>Сложность</td>
                    <td>Приступили</td>
                    <td>Завершили</td>
                    <td>Выполнили</td>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td>правка</td>
                    <td>легко</td>
                    <td>10:25 15.09.2015</td>
                    <td>17:25 16.10.2015</td>
                    <td>5 минут</td>
                </tr>
                <tr>
                    <td>правка 2</td>
                    <td>трудно</td>
                    <td>10:25 15.09.2015</td>
                    <td>17:25 16.10.2015</td>
                    <td>6 часов</td>
                </tr>
                <tr>
                    <td>правка 3</td>
                    <td>средне</td>
                    <td>10:25 15.09.2015</td>
                    <td>17:25 16.10.2015</td>
                    <td>12 часов 9 минут</td>
                </tr>
                </tbody>
            </table>
        </div>

    </div>
</div>


<?php
/**
 * Если не прикреплен к проекту
 * If not attached to the draft
 */
 else:?>
    <div class="row">
        <div class="container">
            <div>
                <?=$task_views[15]?>
            </div>
        </div>
    </div>
<?php endif;?>