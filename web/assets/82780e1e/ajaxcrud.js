/*!
 * Ajax Crud 
 * =================================
 * author aslinye cuma beliau masih bootstrap 3
 * Use for johnitvn/yii2-ajaxcrud extension
 * @author John Martin john.itvn@gmail.com
 */
$(document).ready(function () {

    // Create instance of Modal Remote
    // This instance will be the controller of all business logic of modal
    // Backwards compatible lookup of old ajaxCrubModal ID
    if ($('#ajaxCrubModal').length > 0 && $('#ajaxCrudModal').length == 0) {
        modal = new ModalRemote('#ajaxCrubModal');
    } else {
        modal = new ModalRemote('#ajaxCrudModal');
    }

    // Catch click event on all buttons that want to open a modal
    $(document).on('click', '[role="modal-remote"]', function (event) {
        event.preventDefault();

        // Open modal
        modal.open(this, null);
    });

    // Catch click event on all buttons that want to open a modal
    // with bulk action
    $(document).on('click', '[role="modal-remote-bulk"]', function (event) {
        event.preventDefault();

        // Collect all selected ID's
        var selectedIds = [];
        
        // See if we have a selector set
        var selection = 'selection';
        if ($(this).data("selector") != null) {
        	selection = $(this).data("selector");
        }
        
        $('input:checkbox[name="' + selection + '[]"]').each(function () {
            if (this.checked)
                selectedIds.push($(this).val());
        });

        if (selectedIds.length == 0) {
            // If no selected ID's show warning
            modal.show();
            modal.setTitle('Chưa chọn dữ liệu');
            modal.setContent('Vui lòng chọn dữ liệu để thực hiện hành động này!');
            modal.addFooterButton('Đóng lại', '','btn btn-default', function (button, event) {
                this.hide();
            });
        } else {
            // Open modal
            modal.open(this, selectedIds);
        }
    });
    
     // Create instance of Modal Remote 2
    // This instance will be the controller of all business logic of modal
    // Backwards compatible lookup of old ajaxCrubModal ID
    if ($('#ajaxCrubModal2').length > 0 && $('#ajaxCrudModal2').length == 0) {
        modal2 = new ModalRemote('#ajaxCrubModal2');
    } else {
        modal2 = new ModalRemote('#ajaxCrudModal2');
    }

    // Catch click event on all buttons that want to open a modal
    $(document).on('click', '[role="modal-remote-2"]', function (event) {
        event.preventDefault();

        // Open modal
        modal2.open(this, null);
    });
    
});
