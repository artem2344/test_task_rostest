
function update_doctors_by_filter() {

    let selectedSpecs = jQuery('.filter-check[data-tax="specialization"]:checked').map(function() {
        return jQuery(this).val();
    }).get().join(',');


    let selectedCities = jQuery('.filter-check[data-tax="city"]:checked').map(function() {
        return jQuery(this).val();
    }).get().join(',');

    let sort = jQuery('#doctor-sort').val();
    let currentPath = window.location.pathname.replace(/\/page\/\d+\//g, '/');
    if (!currentPath.endsWith('/')) currentPath += '/';


    let current_url = window.location.origin + window.location.pathname;
    let params = new URLSearchParams();


    if (selectedSpecs) {
        params.append('specialization', selectedSpecs);
    }

    if (selectedCities) {
        params.append('city', selectedCities);
    }

    if (sort && sort !== 'date') { params.set('orderby', sort); }

    window.location.href = window.location.origin + currentPath + '?' + params.toString();
}


jQuery('#doctor-sort').on('change', function() {
    let params = new URLSearchParams(window.location.search);

    params.set('orderby', jQuery(this).val());
    params.delete('paged');

    window.location.href = window.location.pathname + '?' + params.toString();
});






function get_available_filters(target_taxonomy_slug,other_taxonomy_slug) {

    let selectedIds = jQuery('input[data-tax="' + other_taxonomy_slug + '"]:checked')
        .map(function() { return jQuery(this).val(); })
        .get()
        .join(',');


    jQuery.ajax({
        url: '/wp/admin-ajax.php',
        method: 'post',
        dataType: 'json',
        data: {
            action: 'get_filter_data',
            target_taxonomy: target_taxonomy_slug,
            other_taxonomy: other_taxonomy_slug,
            selected_ids: selectedIds
        },
        success: function(data){
        }
    });


}