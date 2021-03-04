var $collectionHolder;

var $addNewItem = $('<a href="#" class="btn btn-info">Add new item</a>');

$(document).ready(function () {
    //get the collectionHolder
    $collectionHolder = $('#creator_list');
    //append to the add new item to the collectionHolder
    $collectionHolder.append($addNewItem);

    $collectionHolder.data('index', $collectionHolder.find('.panel').length);
    //add remove button to existing items
    $collectionHolder.find('.panel').each(function () {
        addRemoveButton($(this));
    });
    //handle the click event for addNewItem
    $addNewItem.click(function (e) {
        e.preventDefault;
        //create a new form and append it to the collectionHolder
        addNewForm();
    });

    $('#filters-form').submit(function (e) {
        var filters = [];
        $.each($( this ).serializeArray(), function(i, field) {
           if (field.value !== '') {
               filters.push(field);
           }
        });
        var filterString = '';
        if (filters.length > 0) {
            filters.forEach(function (filter, index) {
                let cont = '&'
                if (index === 0) {
                    cont = '?'
                }
                filterString += cont + filter.name + '=' + filter.value;
            })
        }
        $(this).attr('action', $(this).attr('action') + filterString);
    });

    $('#remove_filters').click(function (e) {
        e.preventDefault();
        var form = $('#filters-form');
        if (form) {
            var elements = form[0].elements;
            for (var i = 0, element; element = elements[i++];) {
                element.value = null;
            }
            form.submit();
        }
    });

});

function addNewForm() {
    //getting the prototype
    var prototype = $collectionHolder.data('prototype');
    //get the index
    var index = $collectionHolder.data('index');
    //create the form
    var newForm = prototype;

    newForm = newForm.replace(/__name__/g, index);
    $collectionHolder.data('index', index+1);
    //create the panel
    var $panel = $('<div class="panel panel-warning"><div class="panel-heading"></div></div>');

    //create the panel-body and the append the form to it
    var $panelBody = $('<div class="panel-body"></div>').append(newForm);
    //append the body to the panel
    $panel.append($panelBody);
    //append the removeButton to the new panel
    addRemoveButton($panel);
    //append the panel to the addNewItem
    $addNewItem.before($panel);
}


//add new items

//remove items

function addRemoveButton($panel) {
    //create remove button
    var $removeButton = $('<a href="#" class="btn btn-danger">Remove</a>');
    //appendig removeButton to the panel footer
    var $panelFooter = $('<div class="panel-footer"></div>').append($removeButton);
    //handle the click event of the remove button
    $removeButton.click(function (e) {
        e.preventDefault();
        $(e.target).parents('.panel').remove();
    });

    //appent the footer to the panel
    $panel.append($panelFooter);

}