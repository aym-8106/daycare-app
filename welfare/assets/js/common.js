/**
 * @author Kishor Mali
 */


jQuery(document).ready(function(){
	
	jQuery(document).on("click", ".deleteUser", function(){
		var userId = $(this).data("userid"),
			hitURL = baseURL + "admin/company/delete",
			currentRow = $(this);
		
		var confirmation = confirm("この企業を削除してもよろしいですか？");
		
		if(confirmation)
		{
			jQuery.ajax({
			type : "POST",
			dataType : "json",
			url : hitURL,
			data : { userId : userId } 
			}).done(function(data){
				console.log(data);
				currentRow.parents('tr').remove();
				if(data.status = true) {
					alert("正常に削除されました");
					location.href=baseURL + "admin/company";
				}
				else if(data.status = false) { alert("削除に失敗しました"); }
				else { alert("Access denied..!"); }
			});
		}
		return false;
	});
	$().on("click", ".searchList", function(){

	});

	jQuery(document).on("click", ".searchList", function(){
		
	});
	
});
function CopyToClipboard(container_id) {
	$('.code-copy-message').hide();
	if (window.getSelection) {
		if (window.getSelection().empty) { // Chrome
			window.getSelection().empty();
		} else if (window.getSelection().removeAllRanges) { // Firefox
			window.getSelection().removeAllRanges();
		}
	} else if (document.selection) { // IE?
		document.selection.empty();
	}

	if (document.selection) {
		var range = document.body.createTextRange();
		range.moveToElementText(document.getElementById(container_id));
		range.select().createTextRange();
		document.execCommand("copy");
	} else if (window.getSelection) {
		var range = document.createRange();
		range.selectNode(document.getElementById(container_id));
		window.getSelection().addRange(range);
		document.execCommand("copy");
	}
	if (window.getSelection) {
		if (window.getSelection().empty) { // Chrome
			window.getSelection().empty();
		} else if (window.getSelection().removeAllRanges) { // Firefox
			window.getSelection().removeAllRanges();
		}
	} else if (document.selection) { // IE?
		document.selection.empty();
	}
	$('.code-copy-message').show();
	$('.code-copy-message').fadeToggle(1000);
}
