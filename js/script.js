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
 * @param id - там где будет отображаться ошибка (where will show an error)
 */
function addUserTag(request, response, id)
{
    ajaxRequestJSON('task/getUsersProject', undefined, 'hide');
    $.ajax({
        data: {query: request.term },
        success: function (data)
        {
            var error = '';
            if(data.status == 'error')
                error = data.resultTitle;

            if(error != '')
                id.html(error);
            else
            {
                id.html("");
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
 * Прикрепляем юзеров к проекту
 * Users attach to the project
 * @param id - id проекта
 */
function attachUsers(id)
{
    ajaxRequestJSON("task/attachUserProject");
    $.ajax({
        data: {names: $("#tagsInputAutocomplete_"+id).val(), id: id},
        success: function(data)
        {
            errorSuccessAjax({title: data.resultTitle, message: data.resultText}); //показываем модалку
        }
    });
}

/**
 * Прикрепляем юзеров к проекту
 * Users attach to the project
 * @param id - id проекта
 */
function delUserProject(id)
{
    ajaxRequestJSON("task/delUserProject");
    $.ajax({
        data: {names: $("#tagsInputAutocomplete_"+id).val(), id: id},
        success: function(data)
        {
            errorSuccessAjax({title: data.resultTitle, message: data.resultText}); //показываем модалку
        }
    });
}

$(function() {


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

        //автокоплит юзеров
        $('#tagsInputAutocomplete_'+id).tagit({
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
                    //console.log(ui.item);
                    //$("#userAutocompleteHide").val(ui.item.value);
                }
            })});

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
                        console.log(data);
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

    //скрывает автокоплит, если выбран чекбокс (It hides 'input autocomplete' if the checkbox is selected)
    $("#iAdmin").click(function(){
        if(document.getElementById('iAdmin').checked)
            $("#userAutocomplete").prop("disabled", true);
        else
            $("#userAutocomplete").prop("disabled", false);
    });


});