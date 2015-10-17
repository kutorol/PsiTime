/*
* Функция перенаправляет через 10 секунд на главную страницу
*
* @id_ten_sec - span, где заменяется число
* @num - число от 10 до 1
*/
function ten_sec_redirect()
{
		var id_ten_sec = $('#ten_sec_redirect');
		setInterval(function(){
			var num = parseInt(id_ten_sec.html(),10);
			if(num > 1)
			{
				id_ten_sec.html(num-1)
			}
			else{document.location.href = base_url+'index.php/'+segment; return false;}
	}, 1000);		
}


$(document).ready(function(){
		
		/*
		* @new_header - ид картинки хедера для смены
		* @status - удачно\неудачно загрузилось (туда закидываем ошибки и норм)
		* @bar - статус бар загрузки
		*/
		var new_header = $('#new_header');
		
		$("#upl_button").click(function(){ // по нажатию, нажимаем окно добавления файла
				$('#fileupload input').click();
		});
		var status = $('#upl_status');
		var bar = $('#status_upl');
		
		
		$("#dropZone").bind('drop dragover', function (e) { // указал дроп зону
																								
		e.preventDefault();
																								 
		$('#fileupload').fileupload({
				progressall: function (e, data) 
				{
						bar.addClass('in'); // показ статус бара
						var progress = parseInt(data.loaded / data.total * 100, 10); //расчет в процентах статус загрузки
						$('#bar').css('width', progress + '%');  // вывод в статус бар
				},
				done: function (e, data) 
				{
						
						bar.removeClass('in'); // скрываем статус бар
						$('#bar').css('width','0%');  
						var arr_resp = data.result.split("|bad|"); 
												
						if(arr_resp[0]=="error") //если ошибка
						{
							 status.html('<span class="label label-important">'+arr_resp[1]+'</span><br><br>');
							 return false;
						}
                        else if(arr_resp[0] == 'notActiveBlock'){document.location.href = base_url+'index.php/'+segment; return false;}
						else if(arr_resp[0] == 'ban'){ban(); return false;}
						else if(arr_resp[0] == 'ban_author')
						{
							$('#myModalLabel2').html(langAll.banA);
							$('#modal-body').html(langAll.banAMeta);
							$('#myModal2').modal();
							$("#section_for_redirect").html(langAll.redirTen);
							ten_sec_redirect();
						}
						else if(arr_resp[0] == 'reg'){document.location.href = base_url+'index.php/'+segment+'/avtoriz/register'; return false;}
						else if(arr_resp[0] == 'not_author'){document.location.href = base_url+'index.php/'+segment+'/write/new_author/1'; return false;}
						else if(arr_resp[0] == "ok") // если все норм
						{
								new_header.animate({opacity : 0},1000,
													 function()//как только complete animate, вот тогда вкл эта функция
													 {
														 new_header.attr('src', '/img/headers/'+arr_resp[2]);
														 new_header.animate({opacity : 1},1000);
													});
								status.html('<span class="label label-success">'+arr_resp[1]+'</span><br><br>');
						}
						else // хуйня какая то допустим
						{
							status.html('<span class="label label-important">'+langAll.errorAddCommentServer+'</span><br><br>'); 
						}
				}
		});  
																								 
	});
		
		$('#fileupload').fileupload({
				progressall: function (e, data) 
				{
						bar.addClass('in'); // показ статус бара
						var progress = parseInt(data.loaded / data.total * 100, 10); //расчет в процентах статус загрузки
						$('#bar').css('width', progress + '%');  // вывод в статус бар
				},
				done: function (e, data) 
				{
						bar.removeClass('in'); // скрываем статус бар
						$('#bar').css('width','0%');  
						var arr_resp = data.result.split("|bad|"); 
						
						
						if(arr_resp[0]=="error") //если ошибка
						{
							 status.html('<span class="label label-important">'+arr_resp[1]+'</span><br><br>');
							 return false;
						}
                        else if(arr_resp[0] == 'notActiveBlock'){document.location.href = base_url+'index.php/'+segment; return false;}
						else if(arr_resp[0] == 'ban'){ban(); return false;}
						else if(arr_resp[0] == 'ban_author')
						{
							$('#myModalLabel2').html(langAll.banA);
							$('#modal-body').html(langAll.banAMeta);
							$('#myModal2').modal();
							$("#section_for_redirect").html(langAll.redirTen);
							ten_sec_redirect();
						}
						else if(arr_resp[0] == 'reg'){document.location.href = base_url+'index.php/'+segment+'/avtoriz/register'; return false;}
						else if(arr_resp[0] == 'not_author'){document.location.href = base_url+'index.php/'+segment+'/write/new_author/1'; return false;}
						else if(arr_resp[0] == "ok") // если все норм
						{
								new_header.animate({opacity : 0},1000,
													 function()//как только complete animate, вот тогда вкл эта функция
													 {
														 new_header.attr('src','/img/headers/'+arr_resp[2]);
														 new_header.animate({opacity : 1},1000);
													});
								status.html('<span class="label label-success">'+arr_resp[1]+'</span><br><br>');
						}
						else // хуйня какая то допустим
						{
							status.html('<span class="label label-important">'+langAll.errorAddCommentServer+'</span><br><br>'); 
						}
				}
		});  
		
    
});