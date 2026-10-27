
let product_stock_table = new DataTable('#product-stock-table');

let product_stock_alert_table = new DataTable('#product-stock-alert-table,#product-stock-details-table', {
    searching: false,
    autoWidth: false,
    scrollX: false,
    buttons: [
        'copy', 'excel', 'pdf'
    ]
});