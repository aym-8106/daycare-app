/********************************************************************************
 *********************************************************************************/
var wix_url = "https://faq.prozbot.com/api/v1/";
var auth = '{"keyId": "1b00069e-b475-457b-b170-437bb4c56456", "secret": "B2A2XAWIKlekAj7eZ1T9gO6VSMBUxqmVPV8vjHbWqCs"}';

searchArticle("1")

function searchArticle(searchText){

  let auth_param ={
    method : "POST",
    mode: 'cors',
    headers: {
      "Accept": "application/json",
      "Access-Control-Allow-Origin": "https://www.wix.com",
      "Content-Type": "application/json; charset=utf-8",
    },
    body: auth,
  }
  let data = {
    'locale': 'ja',
    'text': searchText,
    "spellcheck": true,
    "page": 1,
    "pageSize": 5,
    "sortType": 100
  }

  console.log("data:", JSON.stringify(data))
  fetch(wix_url+"accounts/token", auth_param)
    .then(function(response) {
      console.log("response:",response)
      return response.json();
    })
    .then(function(jsonResponse) {
      console.log("token:",jsonResponse.token)

      let param ={
        method : "POST",
        mode: 'cors',
        headers: {
          "Accept": "application/json",
          "Access-Control-Allow-Origin": "https://www.wix.com",
          "Content-Type": "application/json; charset=utf-8",
          "Authorization": "Bearer " + jsonResponse.token,
        },
        body: JSON.stringify(data),
      }
      fetch(wix_url+"articles/search", param)
        .then(function(response) {
          return response.json();
        })
        .then(function(json) {
          console.log("search:",json)
        })
        .catch(function(error) {
          console.log("error:",error)
          document.getElementById('messages').value = error;
        });
    })
    .catch(function(error) {
      console.log("error:",error)
      document.getElementById('messages').value = error;
    });



}