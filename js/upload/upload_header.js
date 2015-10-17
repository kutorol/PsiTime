/*
* ������� �������������� ����� 10 ������ �� ������� ��������
*
* @id_ten_sec - span, ��� ���������� �����
* @num - ����� �� 10 �� 1
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
		* @new_header - �� �������� ������ ��� �����
		* @status - ������\�������� ����������� (���� ���������� ������ � ����)
		* @bar - ������ ��� ��������
		*/
		var new_header = $('#new_header');
		
		$("#upl_button").click(function(){ // �� �������, �������� ���� ���������� �����
				$('#fileupload input').click();
		});
		var status = $('#upl_status');
		var bar = $('#status_upl');
		
		
		$("#dropZone").bind('drop dragover', function (e) { // ������ ���� ����
																								
		e.preventDefault();
																								 
		$('#fileupload').fileupload({
				progressall: function (e, data) 
				{
						bar.addClass('in'); // ����� ������ ����
						var progress = parseInt(data.loaded / data.total * 100, 10); //������ � ��������� ������ ��������
						$('#bar').css('width', progress + '%');  // ����� � ������ ���
				},
				done: function (e, data) 
				{
						
						bar.removeClass('in'); // �������� ������ ���
						$('#bar').css('width','0%');  
						var arr_resp = data.result.split("|bad|"); 
												
						if(arr_resp[0]=="error") //���� ������
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
						else if(arr_resp[0] == "ok") // ���� ��� ����
						{
								new_header.animate({opacity : 0},1000,
													 function()//��� ������ complete animate, ��� ����� ��� ��� �������
													 {
														 new_header.attr('src', '/img/headers/'+arr_resp[2]);
														 new_header.animate({opacity : 1},1000);
													});
								status.html('<span class="label label-success">'+arr_resp[1]+'</span><br><br>');
						}
						else // ����� ����� �� ��������
						{
							status.html('<span class="label label-important">'+langAll.errorAddCommentServer+'</span><br><br>'); 
						}
				}
		});  
																								 
	});
		
		$('#fileupload').fileupload({
				progressall: function (e, data) 
				{
						bar.addClass('in'); // ����� ������ ����
						var progress = parseInt(data.loaded / data.total * 100, 10); //������ � ��������� ������ ��������
						$('#bar').css('width', progress + '%');  // ����� � ������ ���
				},
				done: function (e, data) 
				{
						bar.removeClass('in'); // �������� ������ ���
						$('#bar').css('width','0%');  
						var arr_resp = data.result.split("|bad|"); 
						
						
						if(arr_resp[0]=="error") //���� ������
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
						else if(arr_resp[0] == "ok") // ���� ��� ����
						{
								new_header.animate({opacity : 0},1000,
													 function()//��� ������ complete animate, ��� ����� ��� ��� �������
													 {
														 new_header.attr('src','/img/headers/'+arr_resp[2]);
														 new_header.animate({opacity : 1},1000);
													});
								status.html('<span class="label label-success">'+arr_resp[1]+'</span><br><br>');
						}
						else // ����� ����� �� ��������
						{
							status.html('<span class="label label-important">'+langAll.errorAddCommentServer+'</span><br><br>'); 
						}
				}
		});  
		
    
});