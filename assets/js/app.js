



function sendRequest(method, object, form, file) {

    if (method === 'post') {
        var formData = new FormData();
        var form = $('#' + form);
        formData.append('file', $('#' + file)[0].files[0]);
        var processData = false;

    } else if (method === 'get' && form === '') {
        var processData = false;
        var formData = '';
    } else if (method === 'get' && form !== '') {
        var formData = encodeURI('filenames[]=' + form.join('&filenames[]='));
        var processData = true;
    }

    $.ajax({
        url: 'api' + '/' + object + '/',
        method: method,
        processData: processData,
        contentType: false,
        dataType: 'json',
        data: formData,
        success: function (response) {
            console.log(response);

            if (!response['error']) {
                if (response['files']) {
                    printTable(response['files']);
                } else if (response['filenames']) {
                    printFilenames(response['filenames']);
                }


            } else {
                alert(response['error']);
            }


        }
    });

}

function printTable(data) {

    data.forEach(function (entry) {
        //console.log(entry);
        var data3 = new Array();
        var table;
        var data1 = convertArray(entry[0].ResultList.Result.ComponentList.Component);
        var data2 = convertArray(entry[0].Job.MeasureProgram.ComponentList.Component);
        // console.log(data2);
        // console.log(data1);
        for (var key in data1) {
            if (data2[key]) {
                data3.push([key, data1[key].Value, data2[key].Name, data2[key].Unit]);
            }

        }

        if ($.fn.dataTable.isDataTable('#example')) {
            $('#example').dataTable().fnDestroy();
        }

        $('#example').DataTable({
            data: data3,
            columns: [
                {title: "Key"},
                {title: "Value"},
                {title: "Name"},
                {title: "Unit"}
            ]
        });



 $("#filedata").html("");
        var filedata = [

            {key: "ID:", val: entry[0].Id},
            {key: "Format:", val: entry[0].Format},
            {key: "Sender:", val: entry[0].Sender.Name}
        ];


        var markup = "<li><b>${key}</b> ${val}</li>";

        /* Compile the markup as a named template */
        $.template("filedatatemplate", markup);

        /* Render the template with the movies data and insert
         the rendered HTML under the "movieList" element */
        $.tmpl("filedatatemplate", filedata)
                .appendTo("#filedata");

    });



}
function printFilenames(data) {
    $('#filesdatarow').html('');
    $('#filesdata').tmpl(data).appendTo('#filesdatarow');

}
function convertArray(data) {
    var arr = new Array();
    data.forEach(function (entry) {

        arr[entry["@attributes"].Id] = entry;
    });
    return (arr);
}

$('#getfiles').on('click', function () {
    sendRequest('get', 'lists', '', '');
    return true;
});

$('#postfile').on('click', function (event) {
    event.preventDefault();
    sendRequest('post', 'files', 'postfileForm', 'inputFile');
    return true;
});
$(document).on('click','.receivefile', function (event) {
    
    event.preventDefault();

    sendRequest('get', 'files', [this.id], '');
    return true;
});
