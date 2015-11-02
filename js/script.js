/**
 * Удаляем любые данные
 * Remove any data
 *
 * @param url - на какую страницу делаем запрос (on what page we do request)
 * @param selector - как называется селектор той строки, которую впоследствии из вида удалим (the name of the selector line, which was later remove from view)
 * @param id - ид того, что удаляем (id that remove)
 */
function deleteData(url, selector, id)
{
    bootbox.confirm(jsLang[20], function(result) {
        if(result)
        {
            ajaxRequestJSON(url);
            $.ajax({
                // параметры запроса, передаваемые на сервер (последний - подстрока для поиска):
                data: {id: id},
                // обработка успешного выполнения запроса
                success: function(data){
                    if(data.status == 'error')
                        errorSuccessAjax({title: data.resultTitle, message: data.resultText}); //показываем модалку
                    else
                    {
                        var rowProject = $("#"+selector+id);
                        rowProject.fadeOut(300, function(){
                            rowProject.next().remove();
                            rowProject.remove();

                            if($(".project").html() === undefined)
                                $("#addProject").remove();

                            errorSuccessAjax(data.resultTitle, {del: "danger", add: "success"}); //сообщение вставляем под главный title
                        });
                    }
                }
            });
        }
    });

}

/**
 * Для всех ajax запросов это вроде как шаблон
 * For all ajax requests this kind of pattern
 *
 * @param url
 * @param additionalDelBlock - дополнительный блок, подлежащий скрытию
 * @param hidePreloader - если не равно undefined, то показываем прелоадер на весь экран
 */
function ajaxRequestJSON(url, additionalDelBlock, hidePreloader)
{
    var message;
    $.ajaxSetup({
        url: base_url+"/"+url,
        global: false,
        type: "POST",
        dataType: "json",
        beforeSend: function(){
            if(additionalDelBlock !== undefined)
                additionalDelBlock.fadeOut();

            if(hidePreloader === undefined)
                showLoad();
        },
        error: function(e, x, settings, exception){
            hideLoad();//скрываем прелоадер
            var statusErrorMap = {
                '400' : jsLang[8],
                '401' : jsLang[9],
                '403' : jsLang[10],
                '404' : jsLang[11],
                '500' : jsLang[12],
                '503' : jsLang[13]
            };

            if (e.status)
            {
                message = statusErrorMap[e.status];
                if(!message)
                    message = jsLangAdditional;
            }
            else if(exception == 'parsererror')
                message = jsLang[14];
            else if(exception == 'timeout')
                message = jsLang[15];
            else if(exception == 'abort')
                message = jsLang[16];
            else
                message = jsLangAdditional;

            //показываем ошибку
            addTitle({title: jsLang[7], message: message});
        }
    });

}

/**
 * Показываем прелоадер
 */
function showLoad()
{
    $("#prel").fadeIn(500);
}

/**
 * скрываем прелоадер
 */
function hideLoad()
{
    $("#prel").fadeOut(500);
}

/**
 * Функция показывает модальное окно
 * The function displays a modal window
 * @param title
 * @param content
 * @param footer
 */
function showModal(title, content, footer)
{
    $("#myModal .modal-title").html(title);
    $("#myModal .modal-body").attr("align", "center").html('<p>'+content+'</p>');
    if(footer !== undefined)
        $("#myModal .modal-footer").html('<p>'+content+'</p>');
    $('#myModal').modal();
}

/**
 * Вставляет сообщение под заголовок и поднимает вверх
 *
 * @param response - текст
 * @param classHtml - класс стилей для отображения в json {del: '', add: ''}
 */
function addTitle(response, classHtml)
{
    var mainError = $("#mainError"); //ид блока под главным названием, чтобы показывать ошибку

    //если были дополнительные ошибки, то их удаляем, а то мешаются своей громозкостью
    $.each($(".additionalError"), function( index, value ) {
        $(value).remove();
    });

    mainError.html(""); //обнуляем прошлый текст ошибки
    if(classHtml === undefined)
        bootbox.alert({
                title: response.title,
                message: response.message
            });
    else
    {
        mainError.removeClass("label-"+classHtml.del).addClass("label-"+classHtml.add).html(response);
        $('html, body').animate({scrollTop: 0}, 300);
    }
}

/**
 * Вставляет сообщение под заголовок, скрывает кнопку загрузки страницы и поднимает к самому верху
 * @param response - содержит овтет в json
 * @param classHtml - json {del: '', add: ''}
 * @param showForm - если не undefined, то показываем скрытые элементы
 */
function errorSuccessAjax(response, classHtml, showForm)
{
    if(showForm !== undefined)
        $("#"+showForm).fadeIn(150);

    addTitle(response, classHtml);
    hideLoad();
}

/**
 * Достаем имена пользователей для автокомплита
 * We get the usernames for auto complete
 *
 * @param request
 * @param response
 * @param idError - там где будет отображаться ошибка (where will show an error)
 */
function addUserTag(request, response, idError)
{
    ajaxRequestJSON('task/getUsersProject', undefined, 'hide');
    $.ajax({
        data: {query: request.term },
        success: function (data)
        {
            var error = '';
            if(data.status == 'error')
            {
                idError.removeClass('label-success').addClass("label-danger");
                error = data.resultTitle;
            }
            else
                idError.removeClass('label-danger').addClass("label-success");

            if(error != '')
                idError.html(error);
            else
            {
                idError.html("");
                response($.map(data.users, function (item){
                        return {
                            label: item.name + item.login,
                            value: item.login
                        }
                    })
                );
            }
        },
        error: function (request, status, error)
        {
            //показываем ошибку
            addTitle({title: jsLang[7], message: error});
        }});
}

/**
 * Показывает ошибку или сообщение при удалении или добавлении тега (юзера к проекту)
 * It displays an error message or when you add or remove the tag (user to the project)
 * @param modal - показывать модальное окно или же в html встроить сообщение
 * @param data - данные с сообщением от скрипта
 * @param id - ид проекта
 * @param selector - ид DOM элемента, куда вставляется ошибка
 * @returns {boolean}
 */
function showMessageInputTag(modal, data, id, selector)
{
    var fail = false;
    if(modal === undefined)
        errorSuccessAjax({title: data.resultTitle, message: data.resultText}); //показываем модалку
    else
    {
        var label = $("#"+selector+id);
        if(data.status != 'error')
            label.removeClass('label-danger').addClass("label-success");
        else
        {
            label.removeClass('label-success').addClass("label-danger");
            fail = true;
        }

        label.html(data.resultTitle+" "+data.resultText);
    }

    return fail;
}

/**
 * Удаляем юзеров из проекта
 * Remove users from the project
 * @param id - id проекта
 * @param name - delete user name
 * @param modal - show or no modal view message
 */
function delUserProject(id, name, modal)
{
    ajaxRequestJSON("task/delUserProject", undefined, (modal !== undefined) ? 'dontShow' : modal);
    var fail = false;
    $.ajax({
        data: {names: name, id: id},
        success: function(data)
        {
            fail = showMessageInputTag(modal, data, id, 'tagsInputAutocompleteError_');
        }
    });

    return fail;
}


/**
 * Прикрепляем юзеров к проекту, над input добавляет текст ошибки или успеха или показывает модальное окно
 * Users attach to the project, adds the text of the input error or success, or show the modal window
 * @param id - id проекта
 * @param name - attach user name
 * @param modal - show or no modal view message
 */
function attachUsers(id, name, modal)
{
    ajaxRequestJSON("task/attachUserProject", undefined, (modal !== undefined) ? 'dontShow' : modal);
    $.ajax({
        data: {names: name, id: id},
        success: function(data)
        {
            showMessageInputTag(modal, data, id, 'tagsInputAutocompleteError_');
        }
    });

}


/**
 * Получаем все задачи для всех проектов
 * We get all the tasks for all projects
 */
function getAllTask(idProject, from)
{
    from = (from === undefined) ? 0 : parseInt(from);
    var idProject = (idProject === undefined) ? 0 : parseInt(idProject);
    //шаблон ajax запроса
    ajaxRequestJSON("task/getAllTask");
    $.ajaxSetup({
        dataType: "html"
    });
    $.ajax({
        data: {idProject: idProject, curent_page: from, from: from},
        success: function(data)
        {
            var jsonResponse = $.parseJSON(data);
            if(jsonResponse.status == 'error')
            {
                errorSuccessAjax({title: jsonResponse.resultTitle, message: jsonResponse.resultText}); //показываем модалку
                return false;
            }

            //вставляем количество заданий у всех проектов в левую навигацию
            if(jsonResponse.countProject_all !== undefined)
            {
                $("#countProject_all").html(jsonResponse.countProject_all);
                if(jsonResponse.countProject_all > 0)
                {
                    var allProjectId = jsonResponse['idProjects'].split('|');
                    $.each(allProjectId, function( index, value ) {
                        if(jsonResponse['countProject_'+value] !== undefined)
                            $("#countProject_"+value).html(jsonResponse['countProject_'+value]);
                    });
                }
            }

            $("#allTaskHere").html(jsonResponse.content);
            hideLoad();
        }
    });
}

/**
 * Проверяем число ли это и находиться ли оно в заданном диапазоне
 * Check whether this number and whether it is in a predetermined range
 * @param title - название поля (field name)
 * @param num - проверяемое значение (the value to test)
 * @param fail - true|false была ли ошибка ранее (whether the error earlier)
 * @param zero - проверять ли на то, что меньше или равно 0 (check whether that is less than or equal to 0)
 * @param before - до какого значения должно быть число (to which value should be a number)
 * @param after - ниже какого числа, проверяемое значение не может быть (below a number, verifiable value can not be)
 * @returns {{message: string, fail: *}}
 */
function validateNum(title, num, fail, zero, before, after)
{
    var errorMessage = '';
    if(isNaN(num)) //если не число
    {
        errorMessage = jsLang[28]+" <i>'"+title+"'</i>";
        fail = true;
    }
    else // если число
    {
        if(zero !== undefined)
        {
            if(num <= 0)
            {
                errorMessage += jsLang[29]+" <i>'"+title+"'</i> "+jsLang[30];
                fail = true;
            }
        }

        if(before !== undefined && after !== undefined)
        {
            if(num < after || num > before)
            {
                errorMessage += jsLang[29]+" <i>'"+title+"'</i> "+jsLang[31]+" " + after + " "+jsLang[32]+" " + before;
                fail = true;
            }
        }
        else if(before !== undefined)
        {
            if(num > before)
            {
                errorMessage += jsLang[29]+" <i>'"+title+"'</i> " + jsLang[33] + " " + before;
                fail = true;
            }
        }
        else if(after !== undefined)
        {
            if(num < after)
            {
                errorMessage += jsLang[29]+" <i>'"+title+"'</i> " + jsLang[31] + " " + after;
                fail = true;
            }
        }
    }

    return {message: errorMessage, fail: fail};
}


/**
 * При нажатии на иконку картинки, в модальном окне показывается увеличенная картинка. Если это другой файл, то либо его показываем, либо скачиваем
 * Clicking on the icon image in a modal window shows an enlarged picture. If it's another file, or a show, or download the
 * @param src
 * @param ext
 * @param title
 * @param idTask - id задачи, чтобы скачать файл по правильному пути
 * @returns {boolean}
 */
function showDownloadImageDoc(src, ext, title, idTask)
{
    var srcFile = src.split('/');
    srcFile = srcFile[srcFile.length-1];
    var windowParam = 'width=500,height=600,resizable=yes,scrollbars=yes,status=yes';
    var secondContetn = '<br><br><div onclick="window.open(\''+base_url+'/task/download/'+idTask+'/'+srcFile+'\', \''+jsLang[23]+' '+title+'\', \''+windowParam+'\');" class="btn btn-warning">'+jsLang[23]+' <i class="fa fa-download"></i></div>';

    var content;
    switch (ext)
    {
        case 'img':
            content = '<img src="' + src + '" class="img-responsive"/>'; break;
        case 'audio':
            content = '<audio controls><source src="'+src+'" preload="metadata">'+jsLang[22]+'</audio>'; break;
        case 'video':
            content = '<video src="'+src+'" width="640" height="360" controls />'; break;
        default:
            window.open(base_url+'/task/download/'+idTask+'/'+srcFile, jsLang[23]+' '+title, windowParam);
            return false;
    }

    showModal(title, content+secondContetn);
}

$(function() {

    $(".statusLevelInList").on('change', function(){
        alert($(this).val());
    });

    /**
     * Функция открывает и закрывает поля для редактирования конкретной задачи
     * This function opens and closes the field to edit a particular task
     */
    $(".editTaskA").on('click', function(e){
        if($(this).attr('data-switch') == 'open')
        {
            $("#showFadeEditTask").slideDown(300);
            $(this).attr('data-switch', 'close').find("i").attr('class', "fa fa-arrow-up");
            $(this).find("span.edit").html(jsLang[44]);

        }
        else
        {
            $("#showFadeEditTask").slideUp(300);
            $(this).attr('data-switch', 'open').find("i").attr('class', "fa fa-arrow-down");
            $(this).find("span.edit").html(jsLang[45]);
        }

        e.preventDefault();
    });

    /**
     * При нажатии на кнопку закрыть в модальном окне - убираем все содержимое
     * When you click on the Close button in a modal window - remove all the contents
     */
    $("#myModal").on('click', function(){
        setTimeout(function(){
            if(!$("#myModal").hasClass('in'))
            {
                $("#myModal .modal-title").html('');
                $("#myModal .modal-body").attr("align","").html('');
            }
        }, 500);
    });

    /**
     * Показывает или скрывает форму добавления нового задания для проекта
     * Shows or hides the form to add a new task for the project
     */
    $("#addTaskBtnForm").on('click', function(){
        var allTasks = $("#allTasks"), addTaskForm = $("#addTaskForm");

        if($(this).hasClass("btn-warning"))
        {
            $(this).html(jsLang[26]+" <i class='fa fa-times'></i>").removeClass("btn-warning").addClass("btn-danger");
            allTasks.fadeOut(150, function(){
                addTaskForm.fadeIn(150);
            });
        }
        else
        {
            $(this).html(jsLang[25]+" <i class='fa fa-plus'></i>").removeClass("btn-danger").addClass("btn-warning");

            addTaskForm.fadeOut(150, function(){
                allTasks.fadeIn(150);
                getAllTask();
                //делаем активной вкладку "все проекты", т.к. после добавления задачи именно они достаются.
                $("#allProjectsTasks").click();
            });
        }
    });

    /**
     * Когда выбираем нужную нам сложность, то цвет у самого select становиться цветом выбранного option
     * When we select the desired complexity, the color had become a very select color selected option
     */
    $("#taskLevel option").click(function(){
        var color = $(this).attr("class");
        $('#taskLevel').attr("class", "form-control "+color);
    });

    /**
     * Удаляем и добавляем класс active для меню, при выборе проекта
     * Remove and add the active class for the menu, selecting Project
     */
    $("#menu-projects a").click(function(){
        $.each($("#menu-projects a"), function( index, value ) {
            $(value).removeClass("active");
        });
        $(this).addClass("active");
        return false;
    });

    /**
     * Если false, то разрешается event beforeTagRemoved, иначе его не выполнять. Если этого не сделать, то сообщение при завершении скрипта всегда будет с ошибкой
     * If false, then allowed event before Tag Removed, otherwise it will not perform. If you do not, then the message at the end of the script will always be an error
     * @type {boolean}
     */
    var dontDeleteUserTag = false;

    /**
     * При нажатии кнопки добавить юзера к проекту
     * When you click to add a user to the project
     */
    $(".toogle-user").click(function() {

        var id = $(this).parent().attr('data-id');
        var idAttach;
        //скрываем все поля добавления юзера к проекту, кроме выбранного
        $.each($(".addUserProject"), function( index, value ) {
            idAttach = $(value).attr("data-id");
            if(idAttach != id)
                $("#addUserProject_"+idAttach).hide(500).addClass("hidden_my");
        });

        //открываем поле добавления юзера или скрываем
        var selector = $("#addUserProject_"+id);
        if(selector.hasClass("hidden_my"))
            selector.show(500).removeClass("hidden_my");
        else
            selector.hide(500).addClass("hidden_my");

        var inputTags = $('#tagsInputAutocomplete_'+id);
        //автокоплит юзеров
        inputTags.tagit({
            /**
             * до удаления тега отсылаем его в контроллер и если ок, тогда удаляем тег из input
             * to remove the tag sends it to the controller, and if OK, then remove the tag from the input
             * @param event
             * @param ui
             * @returns {boolean}
             */
            beforeTagRemoved : function(event, ui) {
                //смотри выше про эту переменную (See above about the variable)
                if(dontDeleteUserTag === false)
                {
                    //удаляем наш тег из общего стека и передаем его в фунецию
                    var allTags = inputTags.val().split(',');
                    $.each(allTags, function( index, value ) {
                        if(value == ui.tagLabel)
                            allTags.splice(index, 1);
                    });
                    //объединяем в строку
                    allTags = allTags.join(',');

                    var fail = delUserProject(id, allTags, 'noModal'); //without modal window
                    //var fail = delUserProject(id, ""); // with modal window
                    if(fail === true) //if error
                        return false;
                }
            },
            placeholderText: jsLang[19],
            autocomplete: ({
                source: function (request, response)
                {
                    addUserTag(request, response, $("#tagsInputAutocompleteError_"+id));
                },
                minLength: 3,
                autoFocus: true,
                delay: 200,
                select: function (e, ui)
                {
                    dontDeleteUserTag = false;
                    attachUsers(id, ui.item.value, 'noModal'); //without modal message
                    //attachUsers(id, ui.item.value); // with modal message
                }
            })});
    });

    /**
     * Одной кнопкой удаляем все теги
     * One button to remove all tags
     */
    $(".delUserProject").click(function(){
        var id = $(this).attr('data-id');
        var fail = delUserProject(id, "", 'noModal'); //without modal window
        //var fail = delUserProject(id, ""); // with modal window
        //если все прошло успешно, то удаляем все теги, иначе оставляем их
        if(fail === false)
        {
            //смотри выше про эту переменную (See above about the variable)
            dontDeleteUserTag = true;
            $("#tagsInputAutocomplete_"+id).tagit("removeAll");
        }
    });

    /**
     * При нажатии на переименовать скрываем ссылку и показываем инпут, а так же сохраняем результат
     * When you click on a link and rename hide show INPUT, as well as store the result
     */
    $(".btnReName").click(function(){
        var id = $(this).attr("data-id"); //ну бля, ид естесно
        var objLink = $("#nameProject_"+id); //ссылка, которую скрываем и показываем инпут
        var objInput = $("#reName__"+id); //инпут, который меняем
        var lastNameProject = objLink.html(); //название проекта старое
        var btnSave = $("#reNameSave_"+id); //кнопка сохранения

        //если жмякнули по кнопке переименовать
        if($(this).html() == jsLang[3])
        {
            //скрываем ссылку и показываем инпут
            objLink.fadeOut(150, function(){
                objInput.fadeIn(150,function(){
                    objInput.focus().val(objInput.val()); //делаем фокус на инпут и ставим курсор в конец
                });
            });

            //скрываем кнопки, показываем кнопку сохранить
            $(this).parent().fadeOut(150, function(){
                btnSave.fadeIn(150);
            });
        }
        //если жмем по кнопке сохранить
        else
        {
            var titleProject = $.trim(objInput.val()); //получаем "измененное название"
            if(titleProject == '' || /^[a-zA-Zа-яА-ЯёЁ0-9-_ ]{3,256}$/.test(titleProject) === false)
            {
                addTitle({title: jsLang[5], message: jsLang[6]}); //показываем ошибку
                objInput.val(lastNameProject); //заносим в инпут старое название
                return false;
            }

            //если изменили название
            if(titleProject != lastNameProject)
            {
                //шаблон ajax запроса
                ajaxRequestJSON("task/updateProject", btnSave);
                $.ajax({
                    data: {id: id, title: titleProject},
                    success: function(data)
                    {
                        if(data.status == 'error')
                        {
                            objInput.val(lastNameProject);
                            errorSuccessAjax({title: data.resultTitle, message: data.resultText}, undefined, 'groupBtn_'+id); //показываем модалку
                        }
                        else
                        {
                            objLink.html(titleProject);
                            errorSuccessAjax(data.resultTitle, {del: "danger", add: "success"}, 'groupBtn_'+id); //сообщение вставляем под главный title
                        }

                        //скрываем ссылку и показываем инпут
                        objInput.fadeOut(150, function(){
                            objLink.fadeIn(150);
                        });

                        btnSave.fadeOut(150); //скрываем кнопку сохранить
                    }
                });
                return false;
            }

            //если название не изменили показываем конфирм
            bootbox.confirm(jsLang[17], function(result) {
                if(result)
                {
                    btnSave.fadeOut(150, function(){
                        errorSuccessAjax(jsLang[18], {del: "danger", add: "success"}, 'groupBtn_'+id); //сообщение вставляем под главный title
                    });

                    objInput.fadeOut(150, function(){
                        objLink.fadeIn(150);
                    });
                }
            });
        }
    });


    /**
     * Автокомплит, который делает юзера главным по проекту
     * Autocomplete, which makes the user the main project
     */
    $( "#userAutocomplete" ).autocomplete({
        source: function(request, response)
        {
            addUserTag(request, response, $("#autocomplete_error"));
        },
        minLength: 3, //срабатывание при минимальном количестве символов
        delay: 200, //задержка между запросами
        autoFocus: true
        /*select: function (e, ui)
        {
            $("#userAutocompleteHide").val(ui.item.value);
        }*/
    });

    /**
     * скрывает автокоплит, если выбран чекбокс
     * It hides 'input autocomplete' if the checkbox is selected
     */
    $("#iAdmin").click(function(){
        if(document.getElementById('iAdmin').checked)
            $("#userAutocomplete").prop("disabled", true);
        else
            $("#userAutocomplete").prop("disabled", false);
    });


});