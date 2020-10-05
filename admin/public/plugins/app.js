function openModal(id) {
    let modal = $(id);
    let cont = modal.find('.modal-content');
    cont.empty();
    cont.append('<img src="public/images/loading.svg" style="width: 40px;display: block;margin: 0 auto" class="rotating">');
    modal.addClass('active');
}
function closeModal(id) {
    let modal = $(id);
    modal.removeClass('active');
}