var loadDoan = function () {
    $('#doan_table').DataTable( {
        serverSide: true,
        ajax: {
            url: 'listsDoan',
            type: 'GET'
        },
        columns: [
            { "data": "d_number" },
            { "data": "d_nhan" },

            // { "data": "vt_nhan" },
            // { "data": "created_at" }
        ],
        "order":false
    } );
}