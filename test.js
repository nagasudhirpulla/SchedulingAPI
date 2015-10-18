// The root URL for the RESTful services
var rootURL = "http://localhost/api/names";
function findAll() {
    console.log('findAll');
    $.ajax({
        type: 'GET',
        url: rootURL,
        dataType: "json", // data type of response
        success: renderList
    });
}

function create(){
    console.log('createOne');
    $.ajax({
        type: 'POST',
        url: rootURL,
        dataType: "json", // data type of response
        data:"name="+document.getElementById("input").value,
        success: function(data, textStatus, jqXHR){
            alert('Name created successfully with id '+JSON.stringify(data));
        },
        error: function(jqXHR, textStatus, errorThrown){
            alert('addName error: ' + textStatus);
        }
    });
}

function updateName() {
    $.ajax({
        type: 'PUT',
        contentType: 'application/json',
        url: rootURL+'/1',
        dataType: "json",
        data: "name="+document.getElementById("input").value+"&updatename="+document.getElementById("updateinput").value,
        //data : JSON.stringify({name:document.getElementById("input").value,updatename:document.getElementById("updateinput").value}),
        success: function(data, textStatus, jqXHR){
            alert('Name updated successfully.Number of rows updated are '+JSON.stringify(data));
        },
        error: function(jqXHR, textStatus, errorThrown){
            alert('updateName error: ' + textStatus);
        }
    });
}

function deleteName() {
    console.log('deleteName');
    $.ajax({
        type: 'DELETE',
        url: rootURL + '/' + document.getElementById("input").value,
        success: function(data, textStatus, jqXHR){
            alert('Name deleted successfully');
        },
        error: function(jqXHR, textStatus, errorThrown){
            alert('deleteName error');
        }
    });
}

function find(){
    console.log('findOne');
    $.ajax({
        type: 'GET',
        url: rootURL+"/"+document.getElementById("input").value,
        dataType: "json", // data type of response
        success: renderList
    });
}

var renderList = function(data) {
    // JAX-RS serializes an empty list as null, and a 'collection of one' as an object (not an 'array of one')
    var list = data == null ? [] : (data.wine instanceof Array ? data.wine : [data.wine]);
    alert(JSON.stringify(data));
};