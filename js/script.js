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
 * @param additionalDelBlock - дополнительный блок, подлежащий скрытию
 */
function ajaxRequestJSON(url, btn_delete_selector, id, additionalDelBlock)
{
    $.ajaxSetup({
        url: base_url+"/"+url,
        global: false,
        type: "POST",
        dataType: "json",
        beforeSend: function(){
            if(additionalDelBlock !== undefined)
                additionalDelBlock.fadeOut();

            showLoad(btn_delete_selector, id, false);
        },
        error: function(){
            showLoad(btn_delete_selector, id, true); //скрываем кнопку загрузки и открываем прежние скрытые блоки
            addTitle({title: "Произошла ошибка!", message: "Попробуйте обновить страницу, если не поможет - сообщите об ошибке на <a href=''>этой страницу</a>"});
        }
    });
}

/**
 * Показываем значек загрузки страницы
 * @param selector
 * @param id
 * @param back - если false, то показываем кнопку загрузки страницы, true - скрываем
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
 * Вставляет сообщение под заголовок и поднимает вверх
 *
 * @param response - текст
 * @param classHtml - класс стилей для отображения в json {del: '', add: ''}
 */
function addTitle(response, classHtml)
{
    var mainError = $("#mainError"); //ид блока под главным названием, чтобы показывать ошибку
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
 * @param btn_delete_selector
 * @param id
 * @param response - содержит овтет в json
 * @param classHtml - json {del: '', add: ''}
 */
function errorSuccessAjax(btn_delete_selector, id, response, classHtml)
{
    addTitle(response, classHtml);
    showLoad(btn_delete_selector, id, true);
}


$(function() {

    $(".toogle-user").click(function() {
        var id = $(this).parent().attr('data-id');

        //popupWrite.classList.toggle("modal-write-us-show");
        $("#addUserProject_"+id).toggle();
        //username.focus();
    });


    /**
     * При нажатии на переминовать скрываем ссылку и показываем инпут, а так же сохраняем результат
     * TODO слова занести в словарь и подставить сюда
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
            if(titleProject == '' || titleProject.length < 3 || titleProject.length > 255 || /^[a-zA-Zа-яА-ЯёЁ0-9-_ ]+$/.test(titleProject) === false)
            {
                addTitle({title: jsLang[5], message: jsLang[6]}); //показываем ошибку
                objInput.val(lastNameProject); //заносим в инпут старое название
                return false;
            }

            //если изменили название
            if(titleProject != lastNameProject)
            {
                //шаблон ajax запроса
                ajaxRequestJSON("task/updateProject", 'groupBtn_', id, btnSave);
                $.ajax({
                    data: {id: id, title: titleProject},
                    success: function(data)
                    {
                        if(data.status == 'error')
                        {
                            objInput.val(lastNameProject);
                            errorSuccessAjax('groupBtn_', id, {title: data.resultTitle, message: data.resultText}); //показываем модалку
                        }
                        else
                        {
                            objLink.html(titleProject);
                            errorSuccessAjax('groupBtn_', id, data.resultTitle, {del: "danger", add: "success"}); //сообщение вставляем под главный title
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
            bootbox.confirm("Название не изменилось. Оставить его в покое?",function(result) {
                if(result)
                {
                    btnSave.fadeOut(150, function(){
                        errorSuccessAjax("groupBtn_", id, "Не стал переминовывать", {del: "danger", add: "success"}); //сообщение вставляем под главный title
                    });
                }
            });
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