/**
 * File : addStaff.js
 * 
 * This file contain the validation of add user form
 * 
 * Using validation plugin : jquery.validate.js
 * 
 * @author Kishor Mali
 */

$(document).ready(function(){
	
	var addStaffForm = $("#addStaff");
	
	var validator = addStaffForm.validate({
		
		rules:{
			company_name :{ required : true, selected : true },
			staff_name :{ required : true },
			staff_password : { required : true },
			staff_password_confirm : {required : true, equalTo: "#staff_password"},
			role : { required : true, selected : true},
			jobtype : { required : true, selected : true},
			employtype : { required : true, selected : true}
		},
		messages:{
			company_name :{ required : "この項目は必須です" },
			staff_name :{ required : "この項目は必須です" },
			staff_password : { required : "この項目は必須です" },
			staff_password_confirm : {required : "この項目は必須です", equalTo: "同じパスワードを入力してください" },
			role : { required : "この項目は必須です", selected : "選択してください。" },
			jobtype : { required : "この項目は必須です", selected : "選択してください。" },
			employtype : { required : "この項目は必須です", selected : "選択してください。" }
		}
	});
});
