/**
 * Удаляем прикрепленные файлы к добавляемой задачи
 * Remove attachments to the task
 * @param src - полный путь к файлу (the full path to the file)
 * @param id - это DOM элемент, который уберем из виду, показав что файл удален (This DOM element that will clean out of sight, showing that the file is deleted)
 */
function delAttach(src, id)
{
    //шаблон ajax запроса
    ajaxRequestJSON("task/delAttach");
    $.ajax({
        data: {src: src},
        success: function(data)
        {
            console.log(data);
            if(data.status == 'error')
            {
                errorSuccessAjax({title: data.resultTitle, message: data.resultText}); //показываем модалку
                return false;
            }

            hideLoad();
            //удаляем прикрепленный файл
            var deleteAttach = $("#"+id);
            deleteAttach.fadeOut(300, function(){
                deleteAttach.remove();
            });
        }
    });
}

/**
 * Если переносим или добавляем файл через кнопку, то эта функция добавит файл к добавляемой задачи, и отобразит его в нужном нам формате (zip, img, video, audio etc.)
 * If you move or add files via the button, this function will add a file to add tasks, and display it in the required format (zip, img, video, audio etc.)
 */
function uploadAttachFile()
{
    //не удалять! если удалить какой нибудь прикрепленный файл, то настройки для ajax сохраняться и прелоадер будет вечно показываться!
    //do not delete! if you remove some sort of attachment, the setup, for ajax preloader will persist forever show!
    $.ajaxSetup({
        beforeSend: function(){}
    });

    //главный id, куда будут вставляться прикрепленные файлы
    var attachFile = $('#fileAttach');
    //прогресс бар
    var progressBar = $('#bar');
    $('#fileupload').fileupload({
        type: "POST",
        dataType: "json",
        error: function(e, x, settings, exception)
        {
            $('#bar').css('width','0%');
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
        },
        progressall: function (e, data)
        {
            var progress = parseInt(data.loaded / data.total * 100, 10); //расчет в процентах статус загрузки
            progressBar.css('width', progress + '%');  // вывод в статус бар
        },
        done: function (e, data)
        {
            hideLoad();
            progressBar.css('width','0%');
            response = data.result;

            addTitle({title: response.resultTitle, message: response.resultText});
            if(response.status == 'error')
                return false;

            var textAppend = '<div class="col-lg-2" id="'+response.id+'"><div title="'+jsLang[21]+'" onclick="delAttach(\''+response.fileSrc+'\', \''+response.id+'\');" class="btn btn-danger deleteAttachFile"><i class="fa fa-times"></i></div><div class="thumbnail" align="center">';
            textAppend += '<div class="options" data-ext="'+response.extension+'" onClick="showDownloadImageDoc(\''+response.fileSrc+'\', \''+response.extension+'\', \''+response.titleFile+'\');" title="'+response.titleFile+'">';

            switch(response.extension)
            {
                case 'pdf':
                    textAppend += '<i class="fa fa-file-pdf-o iconPdf"></i></div>';
                    break;
                case "word":
                    textAppend += '<i class="fa fa-file-word-o iconWord"></i></div>';
                    break;
                case "exel":
                    textAppend += '<i class="fa fa-file-excel-o iconExel"></i></div>';
                    break;
                case "pPoint":
                    textAppend += '<i class="fa fa-file-powerpoint-o iconPPoint"></i></div>';
                    break;
                case "text":
                    textAppend += '<i class="fa fa-file-text-o iconAttach"></i></div>';
                    break;
                case "video":
                    textAppend += '<i class="fa fa-file-video-o iconAttach"></i></div>';
                    break;
                case "audio":
                    textAppend += '<i class="fa fa-file-audio-o iconAttach"></i></div>';
                    break;
                case "img":
                    textAppend += '<img src="'+response.fileSrc+'" alt="'+response.titleFile+'"></div>';
                    break;
                default:
                    textAppend += '<i class="fa fa-file-archive-o iconAttach"></i></div>';
            }

            textAppend += '<div class="longText" title="'+response.titleFile+'" >'+response.titleFile+'</div></div></div>';

            attachFile.append(textAppend);
        }
    });
}

/**
 * При нажатии на иконку картинки, в модальном окне показывается увеличенная картинка. Если это другой файл, то либо его показываем, либо скачиваем
 * Clicking on the icon image in a modal window shows an enlarged picture. If it's another file, or a show, or download the
 */
function showDownloadImageDoc(src, ext, title)
{
    var srcFile = src.split('/');
    srcFile = srcFile[srcFile.length-1];
    var windowParam = 'width=500,height=600,resizable=yes,scrollbars=yes,status=yes';
    var secondContetn = '<br><br><div onclick="window.open(\''+base_url+'/task/download/'+srcFile+'\', \''+jsLang[23]+' '+title+'\', \''+windowParam+'\');" class="btn btn-warning">'+jsLang[23]+' <i class="fa fa-download"></i></div>';

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
            window.open(base_url+'/task/download/'+srcFile, jsLang[23]+' '+title, windowParam);
            return false;
    }

    showModal(title, content+secondContetn);
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

$(document).ready(function(){

     /**
     * Кнопка добавить задачу
     * Button to add the task
     */
    $("#addTaskBtn").on('click', function(){
        var fail = false, errorMessage = "<ul>", tempErrorMessage;

        /**
         * Валидация данных
         */
        //название
        var titleTask = $.trim($("#titleTask").val());
        if(titleTask == '' || /^[a-zA-Zа-яА-ЯёЁ0-9-_ ]{3,256}$/.test(titleTask) === false)
        {
            errorMessage += "<li>" + jsLang[28] + " '<i>"+jsLang[27]+"</i>':<br>" + jsLang[6] + "</li>";
            fail = true;
        }

        //описание
        var descTask =  $.trim($("#addTaskForm textarea").val());

        var idProject = parseInt($("#projectSelect").val());
        tempErrorMessage = validateNum(jsLang[34], idProject, fail, 'yes');
        errorMessage += (tempErrorMessage.message != '') ? "<li>"+tempErrorMessage.message+"</li>" : '';
        fail = tempErrorMessage.fail;

        //сложность
        var taskLevel = parseInt($("#taskLevel").val());
        tempErrorMessage = validateNum(jsLang[35], taskLevel, fail, 'yes');
        errorMessage += (tempErrorMessage.message != '') ? "<li>"+tempErrorMessage.message+"</li>" : '';
        fail = tempErrorMessage.fail;

        var startDay = 'no', endDay = 'no';
        if(!$("#onceTime").hasClass("hidden"))
        {
            startDay = parseInt($("#startDay").val());
            endDay =  parseInt($("#endDay").val());

            tempErrorMessage = validateNum(jsLang[36], startDay, fail, undefined, 24, 0);
            errorMessage += (tempErrorMessage.message != '') ? "<li>"+tempErrorMessage.message+" "+jsLang[38]+"</li>" : '';
            fail = tempErrorMessage.fail;

            tempErrorMessage = validateNum(jsLang[37], endDay, fail, undefined, 24, 0);
            errorMessage += (tempErrorMessage.message != '') ? "<li>"+tempErrorMessage.message+" "+jsLang[38]+"</li>" : '';
            fail = tempErrorMessage.fail;
        }

        //примерное время выполнения
        var estimatedTimeForTask = parseInt($("#estimatedTimeForTask").val());
        tempErrorMessage = validateNum(jsLang[39], estimatedTimeForTask, fail, 'yes');
        errorMessage += (tempErrorMessage.message != '') ? "<li>"+tempErrorMessage.message+"</li>" : '';
        fail = tempErrorMessage.fail;

        //в чем измерять время
        var measurementTime = parseInt($("#measurementTime").val());
        tempErrorMessage = validateNum(jsLang[40], measurementTime, fail);
        errorMessage += (tempErrorMessage.message != '') ? "<li>"+tempErrorMessage.message+"</li>" : '';
        fail = tempErrorMessage.fail;
        switch(measurementTime){case 0: case 1: case 2: case 3: case 4: break;default: measurementTime = 1;}

        //если были ошибки
        if(fail === true)
        {
            errorMessage += "</ul>";
            addTitle({title: jsLang[5], message: errorMessage}); //показываем ошибку
            return false;
        }

        //шаблон ajax запроса
        ajaxRequestJSON("task/addTask");
        $.ajax({
            data: {titleTask: titleTask, descTask: descTask, idProject: idProject, taskLevel: taskLevel, startDay: startDay, endDay: endDay, estimatedTimeForTask: estimatedTimeForTask, measurementTime: measurementTime},
            success: function(data)
            {
                if(data.status == 'error')
                {
                    errorSuccessAjax({title: data.resultTitle, message: data.resultText}); //показываем модалку
                    return false;
                }

                if(data.error !== undefined)
                {
                    data.resultText += "<p><b>"+jsLang[41]+"</b> <ul>";
                    $.each( data.error, function( key, value ) {
                        data.resultText += "<li>" + value + "</li>";
                    });
                    data.resultText += "</ul></p>";
                }

                //очищаем введеные поля
                $("#titleTask").val('');
                $("#addTaskForm textarea").val('');
                $("#estimatedTimeForTask").val('');

                //удаляем прикрепленные файлы из видимости
                var fileAttach = $("#fileAttach");
                fileAttach.fadeOut(300,function(){fileAttach.html('').show();});

                //скрываем блок с заданием времени рабочего дня
                if(data.hideTimeBlock !== undefined)
                    $("#onceTime").addClass("hidden");

                errorSuccessAjax({title: data.resultTitle, message: data.resultText}); //показываем модалку
            }
        });


        return false;
    });

    /**
     * Когда перетаскиваем файл в специальное поле
     * When drag and drop files into a special field
     */
    $("#dropZone").bind('drop dragover', function (e) { // указал дроп зону
        e.preventDefault();
        uploadAttachFile();
    });

    /**
     * При нажатии на фейковую кнопку "Выбрать файл", открываем настоящую кнопку
     * By clicking on phishing web button "Choose File" button to open this
     */
    $("#fake_upload_button").click(function(){ // по нажатию, нажимаем окно добавления файла
        $('#fileupload input').click();
    });

    //эта функция сработает тогда, когда нажмем на фейковую кнопку "Выбрать файл"
    //This function works when fake click on the "Choose File"
    uploadAttachFile();
});