// The root URL for the RESTful services
var rootURL = "http://localhost/apinew.php/names";
function findAll() {
    console.log('findAll');
    $.ajax({
        type: 'GET',
        url: rootURL,
        dataType: "json", // data type of response
        success: renderList
    });
}

function find(){
    console.log('findOne');
    $.ajax({
        type: 'GET',
        url: rootURL+document.getElementById("input").value,
        dataType: "json", // data type of response
        success: renderList
    });
}

var renderList = function(data) {
    // JAX-RS serializes an empty list as null, and a 'collection of one' as an object (not an 'array of one')
    var list = data == null ? [] : (data.wine instanceof Array ? data.wine : [data.wine]);
    alert(JSON.stringify(data));
};