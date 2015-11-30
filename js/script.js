/**
 * Ищем задачи по ключевому слову или идентификатору
 * We are looking for the problem by keyword or ID
 * @returns {boolean}
 */
function getMeMySearch()
{
    var fail = false, errorMessage = "<ul>";
    //название
    var titleTask = $.trim($("#getMySearch").val());
    if(titleTask == '')
    {
        //если поисковая строка пуста, то просто имитируем нажатие на "Все проекты", чтобы получить все задачи
        resetFilters();
        return false;
    }

    if(/^[a-zA-Zа-яА-ЯёЁ0-9-_ ]{3,256}$/.test(titleTask) === false)
    {
        errorMessage += "<li>" + jsLang[28] + " '<i>"+"Введите поисковую фразу"+"</i>':<br>" + jsLang[6] + "</li>";
        fail = true;
    }

    //если были ошибки
    if(fail === true)
    {
        errorMessage += "</ul>";
        addTitle({title: jsLang[5], message: errorMessage}); //показываем ошибку
        return false;
    }

    ajaxRequestJSON("task/searchTask");
    $.ajaxSetup({
        dataType: "html"
    });
    $.ajax({
        // параметры запроса, передаваемые на сервер (последний - подстрока для поиска):
        data: {titleTask: titleTask},
        // обработка успешного выполнения запроса
        success: function(data)
        {
            var jsonResponse = $.parseJSON(data);
            if(jsonResponse.status == 'error')
            {
                errorSuccessAjax({title: jsonResponse.resultTitle, message: jsonResponse.resultText}); //показываем модалку
                return false;
            }

            //вставляем полученные данные в html
            $("#allTaskHere").html(jsonResponse.content);
            hideLoad();
        }
    });
}

/**
 * Удаляем любые данные
 * Remove any data
 *
 * @param url - на какую страницу делаем запрос (on what page we do request)
 * @param selector - как называется селектор той строки, которую впоследствии из вида удалим (the name of the selector line, which was later remove from view)
 * @param id - ид того, что удаляем (id that remove)
 * @param dontRemoveNext - {check: true, second: 5, url: "" }. если не dontRemoveNext.check != undefined, то удаляем просто указанный элемент, если dontRemoveNext.second == числу, то делаем редирект на dontRemoveNext.url или на главную страницу через dontRemoveNext.second секунд
 * @param redirect - если undefined, то перекидываем на главную страницу
 */
function deleteData(url, selector, id, dontRemoveNext, redirect)
{
    bootbox.confirm(jsLang[20], function(result) {
        if(result)
        {
            ajaxRequestJSON(url);
            $.ajax({
                // параметры запроса, передаваемые на сервер (последний - подстрока для поиска):
                data: {id: id, redirect: redirect},
                // обработка успешного выполнения запроса
                success: function(data)
                {
                    if(data.status == 'error')
                    {
                        if(data.additionalParam !== undefined)
                        {
                            //если проект удалили, то редиректим человека через 5 секунд
                            if(data.additionalParam.redirectIndex !== undefined)
                                setTimeout(function(){ document.location.href = base_url+"/task";}, 5000);
                        }

                        errorSuccessAjax({title: data.resultTitle, message: data.resultText}); //показываем модалку
                        return false;
                    }

                    //если есть дополнительный параметр
                    if(dontRemoveNext !== undefined)
                    {
                        //если удаляем другое, а не проект
                        if(dontRemoveNext.check !== undefined)
                        {
                            $("#"+selector+id).fadeOut(300, function(){ $(this).remove(); });
                            //если указан url для редиректа
                            if(dontRemoveNext.url !== undefined )
                            {
                                var toUrl = base_url+dontRemoveNext.url;
                                var second = (dontRemoveNext.second !== undefined) ? dontRemoveNext.second * 1000 : 5000;

                                setTimeout(function(){
                                    document.location.href = toUrl;
                                }, second);
                            }

                            errorSuccessAjax({title: data.resultTitle, message: data.resultText}); //сообщение вставляем под главный title
                        }
                    }
                    //если удаляем проект (или задачу на главной странице)
                    else
                    {
                        var rowProject = $("#"+selector+id);
                        rowProject.fadeOut(300, function(){
                            rowProject.next().remove();
                            rowProject.remove();
                            if($(".project").html() === undefined)
                                $("#addProject").remove();

                            if(data.resultText === undefined)
                                errorSuccessAjax(data.resultTitle, {del: "danger", add: "success"}); //сообщение вставляем под главный title
                            else
                                hideLoad();
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
 * Показываем прелоадер (используется при каждом ajax запросе)
 * show preloader (used with each ajax request)
 */
function showLoad()
{
    $("#prel").fadeIn(500);
}

/**
 * скрываем прелоадер (используется при каждом ajax запросе)
 * hide preloader (used with each ajax request)
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
 * Inserts a message header, and holds up
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
 * Вставляет сообщение под заголовок или показывает ответ в модальном окне, скрывает кнопку загрузки страницы и поднимает к самому верху
 * Inserts a caption or message shows the response in a modal window, hide the button and the page load rises to the very top
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
 * Сработает тогда, когда нажмем на кнопку "применить фильтр"
 * It works when click on the "Apply Filter"
 */
function getAllTaskWithFilter()
{
    var idProject = $("#menu-projects a.active").attr("data-id-project");
    if(idProject === undefined)
        $("#allProjectsTasks").addClass("active");

    if(idProject == 'all')
        idProject = undefined;

    //получаем все задания с фильтрами
    getAllTask(idProject, undefined, true);
}

/**
 * Получаем все активные checkbox и заносим все в один массив, а потом преобразуем в строку
 * We get all active checkbox and enter everything in one array and then convert to a string
 * @returns {{}}
 */
function getAllFilterStr()
{
    var allFilter = {};

    //кладем каждый массив в свою ячейку
    if(arrayForFilterStatus.length > 0)
        allFilter['status'] = JSON.stringify(arrayForFilterStatus);

    if(arrayForFilterComplexity.length > 0)
        allFilter['complexity_id'] = JSON.stringify(arrayForFilterComplexity);

    if(arrayForFilterPriority.length > 0)
        allFilter['priority_id'] = JSON.stringify(arrayForFilterPriority);

    if(arrayForFilterPerformer.length > 0)
        allFilter['performer_id'] = JSON.stringify(arrayForFilterPerformer);


    //если в массиве нет ни одного фильтра
    if(allFilter.priority_id === undefined && allFilter.complexity_id === undefined && allFilter.status === undefined && allFilter.performer_id === undefined)
        allFilter = undefined;
    else
        allFilter = JSON.stringify(allFilter); //преобразуем массив в строку

    return allFilter
}

/**
 * Сохраняем фильтры, чтобы потом ими пользоваться. Отправляем полученные данные на сервер
 * Save filters to then use them. We send the data to the server
 * @param allFilter - тут в строке содержаться все выбранные для сохранения фильтры
 */
function saveMyFilter_2()
{
    //проверяем фильтры
    var allFilter = getAllFilterStrOrError();

    if(allFilter !== false)
    {
        //fixme не могу получить значени из input (пробовал textarea и div с аттрибутом contenteditable), поэтому я прохожусь в цикле и беру самое последнее значение!!!.
        //получаем название фильтра
        var nameFilter;
        $('.nameFilterSaveInput').each(function() {
            nameFilter = $(this).val();
        });

        var fail = false, errorMessage = "<ul>", tempErrorMessage;
        //имя фильтра
        if($.trim(nameFilter) == '' || /^[a-zA-Zа-яА-ЯёЁ0-9-_ !?()]{3,256}$/.test($.trim(nameFilter)) === false)
        {
            errorMessage += "<li>" + jsLang[28] + " '<i>"+jsLang[55]+"</i>':<br>" + jsLang[6] + "</li>";
            fail = true;
        }

        //получаем данные - показывать фильтр по умолчанию или нет
        var num = parseInt($(".radioSaveFilter:checked").val());
        tempErrorMessage = validateNum(jsLang[54], num, fail, "yes", 2, 1);
        errorMessage += (tempErrorMessage.message != '') ? "<li>" + tempErrorMessage.message+"</li>" : '';
        fail = tempErrorMessage.fail;

        //если были ошибки
        if(fail === true)
        {
            errorMessage += "</ul>";
            addTitle({title: jsLang[5], message: errorMessage}); //показываем ошибку
            return false;
        }

        //шаблон ajax запроса
        ajaxRequestJSON("task/saveMyFilter");
        $.ajax({
            data: {nameFilter: nameFilter, defaultFilter: num, allFilter: allFilter},
            success: function(data)
            {
                if(data.status == 'error')
                {
                    errorSuccessAjax({title: data.resultTitle, message: data.resultText}); //показываем модалку
                    return false;
                }

                //закрываем модальное окно и очищаем его содержимое
                closeMyModal();
                //удаляем кнопку сохранить из модального окна
                $("#saveGreenBtnFilter").remove();
                $("#applyFilterBtn").click();
                errorSuccessAjax({title: data.resultTitle, message: data.resultText}); //показываем модалку
            }
        });

    }
}

/**
 * закрываем модальное окно и очищаем его содержимое
 * close the modal window and clear the contents
 */
function closeMyModal()
{
    $("#closeMyModal").click();
    $(".modal-body p").html("");
    $("h4.modal-title").html("");
}

/**
 * Если нет фильтров, то выдаем ошибку в модалке
 * If there is no filter, it produces an error in modalke
 * @returns {*}
 */
function getAllFilterStrOrError()
{
    var allFilter = getAllFilterStr();
    if(allFilter === undefined)
    {
        bootbox.alert({
            title: jsLang[5],
            message: jsLang[56]
        });
        return false;
    }

    return allFilter;
}

/**
 * Показываем модальное окно с формой заполнения
 * Showing a modal window with the form filling
 * @returns {boolean}
 */
function saveMyFilter_1()
{
    //проверяем фильтры
    var allFilter = getAllFilterStrOrError();

    if(allFilter !== false)
    {
        $("#myModal .modal-footer").prepend('<button type="button" class="btn btn-success" id="saveGreenBtnFilter" onclick="saveMyFilter_2();">Сохранить</button>');
        showModal("Сохраняем фильтр", $("#contentSaveFilter").html());
    }
}


/**
 * Получаем все задачи для всех проектов
 * We get all the tasks for all projects
 * @param idProject
 * @param from - на какую страницу перейти (which page to go)
 * @param allFilterBool - если не равно undefined, то ищем задачи вместе с фильтром (if not undefined, then we look for the task together with the filter)
 */
function getAllTask(idProject, from, allFilterBool)
{
    from = (from === undefined) ? 0 : parseInt(from);

    idProject = (idProject === undefined) ? 0 : parseInt(idProject);

    var allFilter = undefined;

    if(allFilterBool !== undefined || arrayForFilterStatus.length > 0 || arrayForFilterComplexity.length > 0 || arrayForFilterPriority.length > 0 || arrayForFilterPerformer.length > 0)
        allFilter = getAllFilterStr();

    //шаблон ajax запроса
    ajaxRequestJSON("task/getAllTask");
    $.ajaxSetup({
        dataType: "html"
    });
    $.ajax({
        data: {idProject: idProject, curent_page: from, from: from, allFilter: allFilter},
        success: function(data)
        {
            var jsonResponse = $.parseJSON(data);
            if(jsonResponse.status == 'error')
            {
                errorSuccessAjax({title: jsonResponse.resultTitle, message: jsonResponse.resultText}); //показываем модалку
                return false;
            }

            //если перешли на неизвестную страницу, то выключаем активную вкладку выбора проекта
            if(jsonResponse.dontUseSelectProject !== undefined)
            {
                $("#menu-projects a.active").removeClass("active");
                $("#menu-projects a span.badge").each(function() {
                    $(this).html("0");
                });
            }


            //если параметры для фильтра не правильно переданы
            if(jsonResponse.errorFilters !== undefined)
            {
                showModal(jsLang[7], jsonResponse.errorFilters);
                //снимаем все фильтры
                $("input[type='checkbox'].activeCheckbox").click();

                //ставим узатель на "Все проекты", в меню слева.
                $.each($("#menu-projects a"), function( index, value ) {
                    $(value).removeClass("active");
                    $(value).find("span.badge").html("0");
                });
                $("#allProjectsTasks").addClass("active");

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
    if(fail === undefined)
        fail = false;

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
        else
        {
            if(num < 0)
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
 * @param ext - расширение файла
 * @param title - название файла
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

/**
 * Функция берет данные из select и в зависимости какой id html элемента - такой параметр в бд и изменяется
 * Function takes data from select and in what dependence of id html of an element - such parameter in a DB and changes
 * @param idElement
 * @returns {boolean}
 */
function changeSelectTask(idElement)
{
    var selectpicker = $("#"+idElement);
    var langText, idAttr = idElement, num;
    if(idAttr == 'taskLevelInfo')
        langText = jsLang[35];
    else if(idAttr == 'statusLevelInfo')
        langText = jsLang[46];
    else if(idAttr == 'priorityLevelInfo')
        langText = jsLang[43];
    else if(idAttr == 'performerUser')
        langText = jsLang[48];
    else
    {
        bootbox.alert({
            title: jsLang[7],
            message: jsLang[47]
        });
        return false;
    }

    //меняем цвет select
    var colorSelect = selectpicker.find("option:selected").attr('data-color');
    selectpicker.next().find("button[data-id='"+idAttr+"']").removeClass(selectpicker.attr('data-style')).addClass(colorSelect);
    selectpicker.attr('data-style', colorSelect);

    var errorMessage = "<ul>";
    num = parseInt(selectpicker.val());
    tempErrorMessage = validateNum(langText, num);
    errorMessage += (tempErrorMessage.message != '') ? "<li>" + tempErrorMessage.message+"</li>" : '';

    //если были ошибки
    if(tempErrorMessage.fail === true)
    {
        errorMessage += "</ul>";
        addTitle({title: jsLang[5], message: errorMessage}); //показываем ошибку
        return false;
    }

    //если стоит на паузе, или выполняется задание, то в id btnPause отображаются кнопки "на паузу" и "снять с паузы"
    var btnPauseInHtml = $("#btnPause");

    //шаблон ajax запроса
    ajaxRequestJSON("task/updateTask/"+idAttr);
    $.ajax({
        data: {num: num, idTask: $("#idTaskInfo").html()},
        success: function(data)
        {
            if(data.status == 'error')
            {
                //если существуют дополнительные параметры
                if(data.additionalParam !== undefined)
                {
                    //если кто то попытался поставить на паузу задачу, когда она уже выполнена, то возвращаем select обратно
                    if(data.additionalParam.returnFinish !== undefined)
                    {
                        selectpicker.find("option:selected").prop('selected', false);
                        selectpicker.find("option[value=2]").prop('selected', true);
                        selectpicker.selectpicker('refresh');
                        selectpicker.next().find("button[data-id='"+idAttr+"']").removeClass("btn-info").add("btn-");
                    }

                    //если проект удалили, то редиректим человека через 5 секунд
                    if(data.additionalParam.redirectIndex !== undefined)
                        setTimeout(function(){ document.location.href = base_url+"/task";}, 5000);
                }

                errorSuccessAjax({title: data.resultTitle, message: data.resultText}); //показываем модалку
                return false;
            }

            //если поменяли юзера, который выполняет данное задание, то обновляем вид картинок юзера
            if(data.newViewImgUser !== undefined)
            {
                //вставляем новый вид, если поменялся юзер
                $("#newViewImgUser").fadeOut(100, function(){
                    $(this).html(data.newViewImgUser).fadeIn(100);
                });
            }

            //если ранее был удален выбор исполнителя, то сейчас его возвращаем
            var change_performer = $("#change_performer");
            if(data.changeUserView !== undefined)
                change_performer.css({'display':'none'}).html(data.changeUserView).fadeIn(300);

            //если статус отличается от "поставлена", то вставляем время начала работы над задачей
            var startTime = $("#startTimeTask");
            if(data.startTime !== undefined && startTime.html() != '')
                startTime.css({'display':'none'}).html(data.startTime).fadeIn(300);

            //если закончили выполнение задания, то вставляем время, когда закончили
            var endTime = $("#endTimeTask");
            if(data.endTime !== undefined)
            {
                endTime.css({'display':'none'}).html(data.endTime).fadeIn(300);
                $("#mainTitle").addClass("priehaliKinaNeBudet"); //добавляем перечеркивание заголовка
            }
            //если еще нет конца выполнения задачи
            else
            {
                //и если мы изменяли статус, то удаляем перечеркивание заголовка и если было, то удаляем время конца работы над задачей
                if(idAttr == 'statusLevelInfo')
                {
                    $("#mainTitle").removeClass("priehaliKinaNeBudet");//удаляем перечеркивание заголовка
                    endTime.fadeOut(300).html("");
                }
            }


            //удаляем статус задачи "поставлена", чтобы больше на него не нажимали
            if(idAttr == 'statusLevelInfo' && num > 0 && selectpicker.find("option[value='0']").val() !== undefined)
            {
                selectpicker.find("option[value='0']").remove();
                selectpicker.selectpicker('refresh');
            }

            //если задача выполнена
            if(idAttr == 'statusLevelInfo' && num == 2)
            {
                //удаляем цвет у статуса задачи (делаем его серым)
                selectpicker.next().find("button[data-id='"+idAttr+"']").removeClass("btn-info").addClass(colorSelect);
                //удаляем выбор исполнителя, т.к. задачу выполнил этот юзер, и чтобы его не сменили случайно
                change_performer.fadeOut(300, function(){ $(this).html(""); });
                btnPauseInHtml.fadeOut(300, function(){ $(this).html(""); });
            }

            //если изменили исполнителя, то возможно удаляем некоторые html элементы, чтобы их не изменяли
            if(data.deleteSelectPerformer !== undefined)
            {
                //вместо выбора статуса задачи, получаем текущее значение и удаляем select
                $("#tutStatus").html($("#statusLevelInfo option:selected").text());
                //удаляем выбор исполнителя
                change_performer.fadeOut(300, function(){ $(this).remove(); });
                //удаляем редактирование задания
                $("#editMyTask").fadeOut(300, function(){ $(this).remove(); });
                //удаляем кнопку "удалить задание"
                $("#deleteTask").fadeOut(300, function(){ $(this).remove(); });
                btnPauseInHtml.fadeOut(300, function(){ $(this).html(""); });
            }
            else
            {
                //если статус задачи еще не выполнен, то удаляем строку, где точное время выполнения задания
                if(idAttr == 'statusLevelInfo' && num != 2)
                {
                    $("#myTimeCompliteForTask").fadeOut(300, function(){ $(this).html(""); });

                    //если ставим на паузу
                    if(num == 3)
                    {
                        //заменяем кнопку на "снять с паузы"
                        btnPauseInHtml.fadeOut(150, function(){
                            $(this).html('<div class="btn btn-success" onclick="removePause();">'+jsLang[52]+'</div><hr>').fadeIn(150);
                        });
                    }
                    //если это не пауза и не "выполнено"
                    else
                    {
                        //заменяем кнопку на "снять с паузы"
                        btnPauseInHtml.fadeOut(150, function(){
                            $(this).html('<div class="btn btn-danger" onclick="doPause();">'+jsLang[51]+'</div><hr>').fadeIn(150);
                        });
                    }
                }
            }

            //вставляем за сколько выполнили задание
            if(data.myTimeCompliteForTask !== undefined)
                $("#myTimeCompliteForTask").css({'display':'none'}).html(jsLang[50]+"<label class='label label-danger small-text'>"+data.myTimeCompliteForTask+"</label>").fadeIn(300);

            hideLoad();
        }
    });
}

/**
 * Редактируем конкретную задачу (название и описание)
 * We edit a specific task (the title and the description)
 * @returns {boolean}
 */
function editDescTask()
{
    var fail = false, errorMessage = "<ul>", title = $("#titleTaskInfo"), titleHtml = $("#tastTitleInfo"), textHtml = $("#taskTextInfo");
    //название
    var titleTask = $.trim(title.val());
    if(titleTask == '' || /^[a-zA-Zа-яА-ЯёЁ0-9-_ !?()]{3,256}$/.test(titleTask) === false)
    {
        errorMessage += "<li>" + jsLang[28] + " '<i>"+jsLang[27]+"</i>':<br>" + jsLang[6] + "</li>";
        fail = true;
        title.val(titleHtml.html());
    }

    //описание
    var descTask =  $.trim($("#textTaskInfo").val());


    //если название и описание не изменилось
    if(titleTask == $.trim(titleHtml.html()) && descTask == $.trim(textHtml.html()))
    {
        errorMessage += "<li>"+jsLang[49]+"</li>";
        fail = true;
    }

    //если были ошибки
    if(fail === true)
    {
        errorMessage += "</ul>";
        addTitle({title: jsLang[5], message: errorMessage}); //показываем ошибку
        return false;
    }

    //шаблон ajax запроса
    ajaxRequestJSON("task/updateTask/description");
    $.ajax({
        data: {num: 1, idTask: $("#idTaskInfo").html(), title: titleTask, text: descTask},
        success: function(data)
        {
            if(data.status == 'error')
            {
                if(data.additionalParam !== undefined)
                {
                    //если проект удалили, то редиректим человека через 5 секунд
                    if(data.additionalParam.redirectIndex !== undefined)
                        setTimeout(function(){ document.location.href = base_url+"/task";}, 5000);
                }

                errorSuccessAjax({title: data.resultTitle, message: data.resultText}); //показываем модалку
                return false;
            }

            //обновляем на странице название
            if(data.newTitle != $.trim(titleHtml.html()))
            {
                $("#tastTitleInfo").fadeOut(150, function(){ $(this).html(data.newTitle.fadeIn(150));});
                $("#changeTitleInfo").fadeOut(150, function(){ $(this).html(data.newTitle.fadeIn(150));  });
            }

            if(data.newText != textHtml.html())
                textHtml.fadeOut(150, function(){ $(this).html(data.newText).fadeIn(150);  });


            //закрываем форму редактирования задачи
            $(".editTaskA").click();
            title.val(data.newTitle);
            $("#textTaskInfo").val(data.newText);

            hideLoad();
        }
    });
}

/**
 * При нажатии на кнопку "поставить на паузу", заменяем select на паузу и выполняем changeSelectTask(idElement)
 * When you click on "pause", replace select pause and perform changeSelectTask (idElement)
 */
function doPause()
{
    //делаем неактивными все выбранные элементы
    $('#statusLevelInfo option:selected').prop("selected", false);
    //выбираем паузу
    $("#statusLevelInfo option[value=3]").prop("selected", true);
    //применяем данное действие
    $("#statusLevelInfo").change();
}

/**
 * Снимаем с паузы и ставим статус "выполняется"
 * Remove from the break and set the status "Running"
 */
function removePause()
{
    //делаем неактивными все выбранные элементы
    $('#statusLevelInfo option:selected').prop("selected", false);
    //выбираем статус "выполняется"
    $("#statusLevelInfo option[value=1]").prop("selected", true);
    //применяем данное действие
    $("#statusLevelInfo").change();
}

/**
 * Показываем задачи в соответствии с тем, какую страницу хочет видеть юзер
 * Showing tasks in accordance with what page the user wants to see
 * @returns {boolean}
 */
function getMeMyPage()
{
    var fail = false,  errorMessage = "<ul>";
    var numberPage = parseInt($("#getMyPage").val());

    //проверяем цифру
    tempErrorMessage = validateNum(jsLang[53], numberPage, fail);
    errorMessage += (tempErrorMessage.message != '') ? "<li>"+tempErrorMessage.message +"</li>" : '';
    fail = tempErrorMessage.fail;

    //если были ошибки
    if(fail === true)
    {
        errorMessage += "</ul>";
        addTitle({title: jsLang[5], message: errorMessage}); //показываем ошибку
        return false;
    }

    //если юзер хочет 5 страницу, то в контроллере число должно быть на 1 меньше
    //if the user wants to page 5, the controller number must be 1 below
    if(numberPage > 0)
        numberPage--;

    //получаем id проекта
    var idProject = $("#menu-projects a.active").attr("data-id-project");
    if(idProject == 'all')
        idProject = undefined;

    //получаем все задания с фильтрами
    getAllTask(idProject, numberPage, true);

    return false;
}

/**
 * Сбрасываем фильтр и показываем изначальные задачи без фильтра
 * Clear the filter and show the initial problem without filter
 */
function resetFilters()
{
    $("input[type=checkbox].activeCheckbox").click();
    //удаляем из массивов данные по фильтрам
    arrayForFilterStatus = [];
    arrayForFilterComplexity = [];
    arrayForFilterPriority = [];
    arrayForFilterPerformer = [];
    $("#allProjectsTasks").click();
}

/**
 * В этот массив будут складываться значения фильтра по статусу
 * This array will be folded filter value status
 * @type {Array}
 */
var arrayForFilterStatus = [];

/**
 * В этот массив будут складываться значения фильтра по сложности
 * This array will be formed on the complexity of the filter values
 * @type {Array}
 */
var arrayForFilterComplexity = [];

/**
 * В этот массив будут складываться значения фильтра по приоритету
 * This array will be folded filter values by priority
 * @type {Array}
 */
var arrayForFilterPriority = [];

/**
 * В этот массив будут складываться значения фильтра по исполнителю
 * This array will be folded filter values by performer
 * @type {Array}
 */
var arrayForFilterPerformer = [];

/**
 * общая функция для добавления данных в массивы фильтров
 * common function to add data to the array of filters
 * @param myThis - идентификатор checkbox, на который кикни
 * @param array - массив определенного фильтра, в который записываем данные
 */
function overallCheckboxClick(myThis, array)
{
    //если уже кликали на этот checkbox, то его деактивируем
    if(myThis.hasClass("activeCheckbox"))
    {
        myThis.removeClass("activeCheckbox");

        myThis.next().find("span.cb-icon-check-empty").attr("style", "");
        myThis.next().find("span.cb-icon-check").css({"display":"none"});

        //удаляем из массива данный checkbox из массива данного фильтра
        $.each(array, function( index, value ) {
            //удаляем из массива ненужное значение фильтра
            if(value == myThis.val())
                array.splice(index, 1);
        });
    }
    else
    {
        myThis.next().find("span.cb-icon-check-empty").css({"display":"none"});
        myThis.next().find("span.cb-icon-check").attr("style", "");
        //добавляем значение фильтра в массив
        myThis.addClass("activeCheckbox");
        array.push(myThis.val());
    }
}



$(function() {


    /**
     ********************************
     *  Фильтры (Filters)
     ********************************
     */

    /**
     * При клике на название фильтра - открываем доступные параметры
     * Clicking on the name of the filter - open the available options
     */
    $(".clickHideShow").on("click", function(){
        var nextEl = $(this).next();
        var icon = $(this).find("i");
        if(nextEl.hasClass("openDiv"))
        {
            icon.removeClass("fa-arrow-up").addClass("fa-arrow-down").attr("style", "");
            nextEl.slideUp(300).removeClass("openDiv");
        }
        else
        {
            icon.removeClass("fa-arrow-down").addClass("fa-arrow-up").css({'color': "#337AB7"});
            nextEl.slideDown(300).addClass("openDiv");
        }
    });


    /**
     * Если кликают на чекбоксы, для фильтра по статусу задачи
     * If you click on the checkboxes to filter tasks by status
     */
    $("#checkboxForFilterStatus input[type=checkbox]").click(function(){
        overallCheckboxClick($(this), arrayForFilterStatus);
    });

    /**
     * Если кликают на чекбоксы, для фильтра по сложности задачи
     * If you click on the checkboxes to filter complexity of the task
     */
    $("#checkboxForFilterComplexity input[type=checkbox]").click(function(){
        overallCheckboxClick($(this), arrayForFilterComplexity);
    });

    /**
     * Если кликают на чекбоксы, для фильтра по приоритету задачи
     * If you click on the checkboxes to filter tasks by priority
     */
    $("#checkboxForFilterPriority input[type=checkbox]").click(function(){
        overallCheckboxClick($(this), arrayForFilterPriority);
    });


    /**
     * Если кликают на чекбоксы, для фильтра по исполнителю задачи
     * If you click on the checkboxes to filter tasks by Performer
     */
    $("#checkboxForFilterPerformer input[type=checkbox]").click(function(){
        overallCheckboxClick($(this), arrayForFilterPerformer);
    });

    /**
     * !!!
     * !!! НЕ ВЫНОСИТЬ ВЫШЕ ФУНКЦИЙ $("#checkboxForFilterPerformer input[type=checkbox]"), $("#checkboxForFilterPriority input[type=checkbox]") И ПРОЧИХ. ИНАЧЕ CHECKBOX НЕ БУДЕТ АКТИВЕН, ДАЖЕ ЕСЛИ ЕГО АКТИВИРОВАЛИ
     * !!! NOT TO TAKE OUT ABOVE FUNCTIONS $("#checkboxForFilterPerformer input[type=checkbox]"), $("#checkboxForFilterPriority input[type=checkbox]") AND OTHER. OTHERWISE CHECKBOX WON'T BE ACTIVE EVEN IF IT WAS ACTIVATED
     * !!!
     *
     * После того как checkbox активен или нет, проверяем все активные checkbox и если их больше 0, то показываем кнопку "сбросить фильтр", а если равно 0, то убираем кнопку
     * Once the checkbox is active or not, check all active checkbox, and if more than 0, then show the "Reset Filter", and if equal to 0, remove the button
     */
    $("input[type=checkbox]").on('click', function(){
        var countActive = 0;
        $.each($("input[type=checkbox]"), function( index, value ) {
            if($(value).hasClass("activeCheckbox"))
                countActive++;
        });

        if(countActive > 0 && !$("#resetFilters").hasClass("openResetBtn"))
            $("#resetFilters").addClass("openResetBtn").slideDown(300);
        else if(countActive == 0)
            $("#resetFilters").removeClass("openResetBtn").slideUp(300);

    });

    /**
     ********************************
     *  Конец Фильтры (END Filters)
     ********************************
     */


    /**
     * Когда внесли изменения в название или в описание конкретной задачи, то по нажатию кнопки сохранить - сохраняем
     * When made changes to the name or description of the specific problem, then clicking Save - Saves
     */
    $("#saveEditTask").on('click', function(){
        editDescTask();
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
                //делаем активной вкладку "все проекты", т.к. после добавления задачи именно они достаются.
                $("#allProjectsTasks").click();
            });
        }
    });


    /**
     * Когда выбираем нужную нам сложность, то цвет у самого select становиться цветом выбранного option. При добавлении новой задачи
     * When we select the desired complexity, the color had become a very select color selected option. When you add a new task
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
     * Одной кнопкой удаляем все теги. Это при удалении людей из проекта
     * One button to remove all tags. It during removal of people from the project
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
     * При нажатии на переименовать скрываем ссылку и показываем инпут, а так же сохраняем результат. Это при переименовании проекта
     * When you click on a link and rename hide show INPUT, as well as store the result. This is when you rename the project
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
     * скрывает автокоплит, если выбран чекбокс (в добавлении проекта)
     * It hides 'input autocomplete' if the checkbox is selected (the addition project)
     */
    $("#iAdmin").click(function(){
        if(document.getElementById('iAdmin').checked)
            $("#userAutocomplete").prop("disabled", true);
        else
            $("#userAutocomplete").prop("disabled", false);
    });


    /**
     * Показываем всех юзеров, с их потраченным временем на все проекты (в графиках)
     * Show all users, their time spent on all projects (in the charts)
     */
    $("#showUsersLink a").on('click', function(e){
        $("#showUsersLink").fadeOut(150, function(){
            $(".liNotDisplayUserTime").fadeIn(150);
        });
        e.preventDefault();
    });

    /**
     * Скрывает всех юзеров, кроме "меня", с их потраченным временем на все проекты (в графиках)
     * It hides all users, except for "I", from their time spent on all projects (in the charts)
     */
    $(".aDisplay a").on('click', function(e){
        $(".liNotDisplayUserTime").fadeOut(150, function(){
            $("#showUsersLink").fadeIn(150);
        });
        e.preventDefault();
    });

    /**
     * Активируем checkbox
     * activate the checkbox
     */
    $('input[type=checkbox]').checkbox();


    /**
     * При клике на любую из сортировок (в графиках)
     * Clicking on any of the sorts (in charts)
     */
    $(".clickLabel").on('click', function(){

        if($(this).hasClass("activeLabel"))
            return false;

        $(".activeLabel").removeClass("activeLabel");
        $(this).addClass("activeLabel");

        //получаем из атрибута id элемента, который нужно показать
        var whatShow = $(this).attr("show");
        var filter = $(this).attr("filter");
        //если мы находимся на странице графиков
        if(whatShow !== undefined && filter === undefined)
        {
            //скрываем все элементы на странице
            $("#allCharts").fadeOut(150, function(){
                //скрываем потомков
                $(this).children().hide();
                //показываем нужный элемент
                $("#"+whatShow).show();
                //показываем все элементы
                $(this).fadeIn(150,function(){
                    //если было нажато на "приоритет", то показываем графики
                    if(whatShow == "content_priority")
                        showPriority();
                    else if(whatShow == "content_complexity")
                        showComplexity();
                });
            });
        }
        //если мы переключаем фильтры на главной странице
        else if(filter !== undefined && whatShow === undefined)
        {
            //обнуляем все массивы с фильтрами
            arrayForFilterStatus = [];
            arrayForFilterComplexity = [];
            arrayForFilterPriority = [];
            arrayForFilterPerformer = [];

            //если нажали по фильтру "без фильтров", то показываем все доступные фильтры в левой части сайта
            if($(this).attr("id") == "withoutFilterLabel")
                $("#showOrHideAllFilter").fadeIn(500);
            else
            {
                //убираем все активные чекбоксы
                $("span.cb-icon-check").each(function(){
                    $(this).css({'display':'none'});
                    $(this).next().attr("style","");
                });
                //убираем со всех активных чекбоксов то, что они активные :D
                $("#showOrHideAllFilter input[type=checkbox].activeCheckbox").removeClass("activeCheckbox");


                //если нажали на сохраненый фильтр, то скрываем выбор фильтров в ручном режиме
                $("#showOrHideAllFilter").fadeOut(500);
                //получаем все фильтры, которые могут быть у этого элемента
                var status = $(this).attr("status");
                var complexity_id = $(this).attr("complexity_id");
                var priority_id = $(this).attr("priority_id");
                var performer_id = $(this).attr("performer_id");

                //если такой фильтр существует, то разбиваем его по запятой и вносим в массив
                if(status !== undefined)
                    arrayForFilterStatus = status.split(",");
                if(complexity_id !== undefined)
                    arrayForFilterComplexity = complexity_id.split(",");
                if(priority_id !== undefined)
                    arrayForFilterPriority = priority_id.split(",");
                if(performer_id !== undefined)
                    arrayForFilterPerformer = performer_id.split(",");
            }



            //делаем активной вкладкой "Все проекты", в меню "Мои проекты"
            $("#menu-projects a.active").removeClass("active");
            $("#allProjectsTasks").addClass("active");
            //получаем задания с сохраненным фильтром
            getAllTask();
        }

    });

    /**
     * Если нажимают на checkbox "показывать графики в 3D" и "показывать кнопку экспорта графика"
     * If you click on the checkbox "show graphics in 3D" and "show the export button graphics"
     */
    $("#showOrNot3DChars, #showOrNotExportChars").on('click', function(){
        var myThis = $(this);
        var num = 1, id = myThis.attr("id");

        if(myThis.hasClass("activeCheckbox"))
        {
            myThis.removeClass("activeCheckbox");
            num = 2;
        }
        else
            myThis.addClass("activeCheckbox");

        //шаблон ajax запроса
        ajaxRequestJSON("welcome/updateUser");
        $.ajax({
            data: {num: num, id: id},
            success: function(data)
            {
                if(data.status == 'error')
                {
                    //если чекбокс активирован, то откатываем его обратно
                    if(myThis.hasClass("activeCheckbox"))
                    {
                        myThis.removeClass("activeCheckbox");
                        myThis.next().find(".cb-icon-check").css({'display':'none'});
                        myThis.next().find(".cb-icon-check-empty").css({'display':'inline-block'});
                    }
                    //если чекбокс деактевирован, то делаем его активным
                    else
                    {
                        myThis.addClass("activeCheckbox");
                        myThis.next().find(".cb-icon-check").css({'display':'inline-block'});
                        myThis.next().find(".cb-icon-check-empty").css({'display':'none'});
                    }

                    errorSuccessAjax({title: data.resultTitle, message: data.resultText}); //показываем модалку
                    return false;
                }

                //если все удачно, то перезагружаем страницу
                document.location.href = base_url+"/chart";

                hideLoad();
            }
        });
    });

    /**
     * Удаляем фильтр, когда нажимаем на крестик на главной странице
     * Remove the filter when the push on the cross on the main page
     */
    $(".removeFilter").on("click", function(){

        var myThis = $(this);
        var idFilter = myThis.attr("data-id");

        bootbox.confirm(jsLang[57], function(result) {
            if(result)
            {
                ajaxRequestJSON("task/deleteMyFilter");
                $.ajax({
                    data: {idFilter: idFilter},
                    success: function(data)
                    {
                        errorSuccessAjax({title: data.resultTitle, message: data.resultText}); //показываем модалку

                        if(data.status == 'error')
                            return false;

                        //если мы удаляем  фильтр, который активен сейчас, то мы активным делаем "без фильтров" и удаляем все фильтры из массивов
                        if(myThis.prev().hasClass("activeLabel"))
                            $("#withoutFilterLabel").click();


                        myThis.prev().remove();
                        myThis.remove();
                    }
                });
            }
        });
    });
});