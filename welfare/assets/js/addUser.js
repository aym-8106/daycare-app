/**
 * File : addUser.js
 * 
 * This file contain the validation of add user form
 * 
 * Using validation plugin : jquery.validate.js
 * 
 * @author Kishor Mali
 */

$(document).ready(function(){
	
	var addUserForm = $("#addUser");
	
	var validator = addUserForm.validate({
		
		rules:{
			fname :{ required : true },
			email : { required : true, email : true, remote : { url : baseURL + "checkEmailExists", type :"post"} },
			password : { required : true },
			cpassword : {required : true, equalTo: "#password"},
			mobile : { required : true, digits : true },
			role : { required : true, selected : true}
		},
		messages:{
			fname :{ required : "この項目は必須です" },
			email : { required : "この項目は必須です", email : "有効なメールアドレスを入力してください", remote : "メールアドレスが常に存在しています。" },
			password : { required : "この項目は必須です" },
			cpassword : {required : "この項目は必須です", equalTo: "同じパスワードを入力してください" },
			mobile : { required : "この項目は必須です", digits : "数字を入力してください。" },
			role : { required : "この項目は必須です", selected : "選択してください。" }
		}
	});
});
