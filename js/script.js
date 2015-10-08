/**
 * Удаляем любые данные
 * Remove any data
 *
 * @param url - на какую страницу делаем запрос (on what page we do request)
 * @param selector - как называется селектор той строки, которую впоследствии из вида удалим (the name of the selector line, which was later remove from view)
 * @param id - ид того, что удаляем (id that remove)
 * @param btn_delete_selector - селектор кнопки удаления, чтобы ее можно было скрыть (selector delete button, so that it can be hidden)
 */
function deleteData(url, selector, id, btn_delete_selector)
{
    $.ajax({
        url: base_url+"/"+url,
        type: "POST",
        dataType: "json",
        // параметры запроса, передаваемые на сервер (последний - подстрока для поиска):
        data: {id: id},
        // обработка успешного выполнения запроса
        beforeSend: function(){
            showLoad(btn_delete_selector, id, false);
        },
        success: function(data){
            if(data.status == 'error')
            {
                $("#mainError").removeClass("label-success").addClass("label-danger").html(data.result);
                showLoad(btn_delete_selector, id, true);
            }
            else
            {
                $("#"+selector+id).fadeOut(300, function(){
                    $("#mainError").removeClass("label-danger").addClass("label-success").html(data.result);
                    $(this).remove();
                    if($(".project").html() === undefined)
                        $("#addProject").remove();

                });
            }
        },
        error: function(){
            $("#mainError").removeClass("label-success").addClass("label-danger").html(jsLang[2]);
            showLoad(btn_delete_selector, id, true);
        }
    });
}

/**
 * Для всех ajax запросов это вроде как шаблон
 * @param url
 * @param btn_delete_selector
 * @param id
 */
function ajaxRequestJSON(url, btn_delete_selector,id)
{
    $.ajaxSetup({
        url: base_url+"/"+url,
        global: false,
        type: "POST",
        dataType: "json",
        beforeSend: function(){
            showLoad(btn_delete_selector, id, false);
        }
    });
}

/**
 * Показываем значек загрузки страницы
 * @param selector
 * @param id
 * @param back
 */
function showLoad(selector, id, back)
{
    if(back === false)
    {
        $("#" + selector + id).fadeOut(150, function(){
            $("#load_" + id).fadeIn(150);
        });
    }
    else
    {
        $("#load_" + id).fadeOut(150, function(){
            $("#" + selector + id).fadeIn(150);
        });
    }
}

/**
 * Вставляет сообщение под заголовок, скрывает кнопку загрузки страницы и поднимает к самому верху
 * @param btn_delete_selector
 * @param id
 * @param response
 * @param classHtml
 */
function errorSuccessAjax(btn_delete_selector, id, response, classHtml)
{
    $("#mainError").removeClass("label-"+(classHtml === undefined) ? 'success' : 'danger').addClass("label-"+(classHtml === undefined) ? 'danger' : 'success').html(response);
    showLoad(btn_delete_selector, id, true);
    $('html, body').animate({scrollTop: 0}, 300);
}


$(function() {


    /**
     * При нажатии на переминовать скрываем ссылку и показываем инпут, а так же сохраняем результат
     * TODO сделать  нормальное скрывание загрузки страницы, уменьшить эту функцию
     *
     */
    $(".btnReName").click(function(){
        var id = $(this).attr("data-id");
        var objLink = $("#nameProject_"+id);
        var objInput = $("#reName__"+id);
        var lastNameProject = objLink.html();

        if($(this).html() == jsLang[3])
        {
            objLink.fadeOut(150, function(){
                objInput.fadeIn(150);
            });

            $(this).fadeOut(150, function(){
                $(this).html(jsLang[4]).fadeIn(150);
            });
        }
        else
        {
            var titleProject = $.trim(objInput.val());
            if(titleProject == '')
            {
                errorSuccessAjax("groupBtn_", id, "Нельзя оставлять поле пустым");
                objInput.val(lastNameProject);
                return false;
            }

            if(titleProject != lastNameProject)
            {
                ajaxRequestJSON("task/updateProject", 'groupBtn_', id);
                $.ajax({
                    data: {id: id, title: titleProject},
                    error: function(){
                        errorSuccessAjax('groupBtn_', id, "Произошла ошибка. Попробуйте обновить страницу");
                        objInput.fadeOut(150, function(){
                            objLink.fadeIn(150);
                        });

                        $(this).html(jsLang[3]);
                    },
                    success: function(data)
                    {
                        if(data.status == 'error')
                        {
                            objInput.val(lastNameProject);
                            errorSuccessAjax('groupBtn_', id, data.result);
                        }
                        else
                        {
                            objLink.html(titleProject);
                            errorSuccessAjax('groupBtn_', id, data.result, true);
                        }

                        objInput.fadeOut(150, function(){
                            objLink.fadeIn(150);
                        });

                        $(this).html(jsLang[3]);
                    }
                });
            }
            else
            {
                objInput.fadeOut(150, function(){
                    objLink.fadeIn(150);
                });

                $(this).fadeOut(150, function(){
                    $(this).html(jsLang[3]).fadeIn(150);
                });
            }

        }
    });


    /**
     * Автокомплит прикрепления юзера к проекту
     * Autocomplete user attachment to the project
     */
    $( "#userAutocomplete" ).autocomplete({
        source: function(request, response){
            $.ajax({
                url: base_url+"/task/getName",
                type: "POST",
                dataType: "json",
                // параметры запроса, передаваемые на сервер (последний - подстрока для поиска):
                data:{
                    maxRows: 12,
                    nameUser: request.term
                },
                // обработка успешного выполнения запроса
                success: function(data){

                    if(data.status == 'error')
                    {
                        $("#autocomplete_error").html(data.result);
                        return false;
                    }

                    // приведем полученные данные к необходимому формату и передадим в предоставленную функцию response
                    response($.map(data.users, function(item){
                        var error = '';

                        switch(item.name)
                        {
                            case "notAjax_EX":
                            case "notPostData_EX":
                                error = jsLang[0];
                                break;
                            case "notMatch_EX":
                                error = jsLang[1];
                                break;
                        }

                        if(error != '')
                            $("#autocomplete_error").html(error);
                        else
                        {
                            $("#autocomplete_error").html('');
                            return{
                                label: item.name+item.login,
                                value: item.login
                            }
                        }

                    }));
                }
            });
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