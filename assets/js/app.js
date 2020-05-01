/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import '../css/app.css';

// Need jQuery? Install it with "yarn add jquery", then uncomment to import it.
// import $ from 'jquery';

console.log('Hello Webpack Encore! Edit me in assets/js/app.js');

// loads the jquery package from node_modules
var $ = require('jquery');

// import the function from greet.js (the .js extension is optional)
// ./ (or ../) means to look for a local file
var greet = require('./greet');

// $(document).ready(function () {
//     $('body').prepend('<h1>' + greet('jill') + '</h1>');
// });

// setup an "add a tag" link
var $addTagLink = $('<a href="#" class="add_tag_link">Add a tag</a>');
var $newLinkLi = $('<li></li>').append($addTagLink);

$(document).ready(function() {
    // Get the ul that holds the collection of tags
    var $collectionHolder = $('ul.tags');

    // add the "add a tag" anchor and li to the tags ul
    $collectionHolder.append($newLinkLi);

    // count the current form inputs we have (e.g. 2), use that as the new
    // index when inserting a new item (e.g. 2)
    $collectionHolder.data('index', $collectionHolder.find(':input').length);

    $addTagLink.on('click', function(e) {
        // prevent the link from creating a "#" on the URL
        e.preventDefault();

        // add a new tag form (see code block below)
        addTagForm($collectionHolder, $newLinkLi);
    });


});

function addTagForm($collectionHolder, $newLinkLi) {
    // Get the data-prototype explained earlier
    var prototype = $collectionHolder.data('prototype');

    // get the new index
    var index = $collectionHolder.data('index');

    // Replace '$$name$$' in the prototype's HTML to
    // instead be a number based on how many items we have
    var newForm = prototype.replace(/__name__/g, index);

    // increase the index with one for the next item
    $collectionHolder.data('index', index + 1);

    // Display the form in the page in an li, before the "Add a tag" link li
    var $newFormLi = $('<li></li>').append(newForm);

    // also add a remove button, just for this example
    $newFormLi.append('<a href="#" class="remove-tag">x</a>');

    $newLinkLi.before($newFormLi);

    // handle the removal, just for this example
    $('.remove-tag').click(function(e) {
        e.preventDefault();

        $(this).parent().remove();

        return false;
    });
}

console.log("test");

// setup an "add a tag" link
var $addCommentLink1 = $('<a href="#" class="add_tag_link">Add a tag</a>');
var $newLinkLi1 = $('<li></li>').append($addCommentLink1);

$(document).ready(function() {
    // Get the ul that holds the collection of tags
    var $collectionHolder1 = $('ul.comments');

    // add the "add a tag" anchor and li to the tags ul
    $collectionHolder1.append($newLinkLi1);

    // count the current form inputs we have (e.g. 2), use that as the new
    // index when inserting a new item (e.g. 2)
    $collectionHolder1.data('index', $collectionHolder1.find(':input').length);

    $addCommentLink1.on('click', function(e) {
        // prevent the link from creating a "#" on the URL
        e.preventDefault();

        // add a new tag form (see code block below)
        addTagForm1($collectionHolder1, $newLinkLi1);
    });


});

function addTagForm1($collectionHolder1, $newLinkLi) {
    // Get the data-prototype explained earlier
    var prototype1 = $collectionHolder1.data('prototype');

    // get the new index
    var index1 = $collectionHolder1.data('index');

    // Replace '$$name$$' in the prototype's HTML to
    // instead be a number based on how many items we have
    var newForm1 = prototype1.replace(/__name__/g, index1);

    // increase the index with one for the next item
    $collectionHolder1.data('index', index1 + 1);

    // Display the form in the page in an li, before the "Add a tag" link li
    var $newFormLi1 = $('<li></li>').append(newForm1);

    // also add a remove button, just for this example
    $newFormLi1.append('<a href="#" class="remove-tag">x</a>');

    $newLinkLi.before($newFormLi1);

    // handle the removal, just for this example
    $('.remove-tag').click(function(e) {
        e.preventDefault();

        $(this).parent().remove();

        return false;
    });
}