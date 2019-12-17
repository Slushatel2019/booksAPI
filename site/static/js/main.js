$(function () {
    $('#showAll').on('click', function () {
        $.ajax({
            url: "api/books/",
            type: "GET",
            success: function (data) {
                if (JSON.parse(data).status == 403) {
                    notSignIn();
                } else {
                    $('#result').empty();
                    $('#result').append($('<table class="table table-striped"> <thead>' +
                        '<tr> <th>#</th> <th>name</th> <th>genre</th> <th>pages</th>' +
                        '<th>Actions</th> </tr> </thead> <tbody id="tbody"> </tbody></table>'));
                    let booksArrayOfObjects = JSON.parse(data).data;
                    $.each(booksArrayOfObjects, function (key, bookObject) {
                        let tds = '';
                        $.each(bookObject, function (index, value) {
                            tds += '<td>' + value + '</td>';
                        });
                        $('#tbody').append($('<tr>' + tds +
                            '<td style="width:10%"><div class="btn-group">' +
                            '<button class="btn btn-success" style="display:none" type="button">Update</button>' +
                            '<button class="btn btn-warning" type="button">Change</button>' +
                            '<button class="btn btn-danger" type="button">Delete</button>' +
                            '</div></td></tr>'));
                    });

                    $('table .btn.btn-warning').on('click', function () {
                        $(this).css("display", "none");
                        $(this).prev().css("display", "");
                        $('table .btn.btn-danger').prop({ disabled: true });
                        $('table .btn.btn-warning').prop({ disabled: true });
                        let i = 2;
                        while (i < 5) {
                            let td = $(this).closest("tr").find("td:nth-child(" + i + ")");
                            let oldName = td.text();
                            td.empty();
                            td.append('<input type="text">');
                            $(td[0]['firstChild']).val(oldName);
                            i++;
                        }
                    });

                    $('table .btn.btn-success').on('click', function () {
                        $(this).css("display", "none");
                        $(this).next().css("display", "");
                        $('table .btn.btn-danger').prop({ disabled: false });
                        $('table .btn.btn-warning').prop({ disabled: false });
                        let i = 2;
                        let bookElements = [];
                        let tr = $(this).closest("tr");
                        let id = tr.find("td:first-child").text();
                        while (i < 5) {
                            let td = tr.find("td:nth-child(" + i + ")");
                            let newName = $(td[0]['firstChild']).val();
                            bookElements[i] = newName;
                            td.empty();
                            td.append(newName);
                            i++;
                        }
                        let newBook = {
                            name: bookElements[2],
                            genre: bookElements[3],
                            pages: bookElements[4],
                        };
                        if (book = checkInput(newBook)) {
                            $.ajax({
                                url: "../api/books/" + id,
                                type: "PUT",
                                data: JSON.stringify(book),
                                success: function (data) {
                                    if (JSON.parse(data).status == 403) {
                                        notSignIn();
                                    } else {
                                        let answer = JSON.parse(data);
                                        if (answer.status == 200 && answer.message['changed book'] == 1) {
                                            alert('book is changed');
                                        }
                                        else {
                                            alert('book is not changed');
                                        }
                                    }
                                }
                            });
                        } else {
                            alert('empty fields or incorrect data');
                            $(this).next().click();

                        }
                    });

                    $('.btn.btn-danger').on('click', function () {
                        if (confirm("Are you sure?")) {
                            let tr = $(this).closest("tr");
                            let id = tr.find("td:first-child").text();
                            $.ajax({
                                url: "api/books/" + id,
                                type: "DELETE",
                                success: function (data) {
                                    if (JSON.parse(data).status == 403) {
                                        notSignIn();
                                    } else {
                                        let answer = JSON.parse(data);
                                        if (answer.status == 200) {
                                            alert(answer.message);
                                            tr.remove();
                                        }
                                        else {
                                            alert(answer.message);
                                        }
                                    }
                                }
                            });
                        }
                        else {
                            return false;
                        }
                    });
                }
            }
        });
    });
    $('#showOne').on('click', function () {
        let bookNumber = $('#bookNumber').val();
        if (bookNumber != false) {
            $('#myModal').modal('hide');
            $.ajax({
                url: "api/books/" + bookNumber,
                type: "GET",
                success: function (data) {
                    if (JSON.parse(data).status == 403) {
                        notSignIn();
                    } else {
                        let book = JSON.parse(data);
                        if (book.data == false) {
                            alert('no book with id = ' + bookNumber);
                        }
                        else {
                            $('#result').empty();
                            $('#result').append($('<table class="table table-striped" id="table">' +
                                '<thead> <tr> <th>#</th> <th>name</th> <th>genre</th>' +
                                '<th>pages</th> </tr> </thead> </table>'));
                            $('#table').append($('<tbody> <tr> <th>' + book.data.id + '</th>' +
                                '<th>' + book.data.name + '</th> <th>' + book.data.genre + '</th>' +
                                '<th>' + book.data.pages + '</th> </tr> </tbody> '));
                        }
                    }
                }
            });
        }
        else {
            alert('enter a book number');
        }
    });
    $('#count').on('click', function () {
        $('#myModal').modal('hide');
        $.ajax({
            url: "api/books/count",
            type: "GET",
            success: function (data) {
                if (JSON.parse(data).status == 403) {
                    notSignIn();
                } else {
                    amount = JSON.parse(data);
                    $('#showCount').empty();
                    $('#showCount').text(amount.data);
                }
            }
        });
    });
    $('#add').on('click', function () {
        let book = {
            name: $('#name').val(),
            genre: $('#genre').val(),
            pages: $('#pages').val(),
        }
        if (checkInput(book)) {
            $('#myModalAdd').modal('hide');
            $.ajax({
                url: "api/books/",
                type: "POST",
                data: JSON.stringify(book),
                success: function (data) {
                    if (JSON.parse(data).status == 403) {
                        notSignIn();
                    } else {
                        alert(JSON.parse(data).message);
                    }
                }
            });
        }
        else {
            alert('empty fields or incorrect data');
        }
    });
    $('#signIn').on('click', function () {
        let user = {
            login: $('#login').val(),
            password: $('#password').val(),
        }
        $('#sign').modal('hide');
        $.ajax({
            url: "api",
            type: "POST",
            data: JSON.stringify(user),
            success: function (data) {
                let response = JSON.parse(data).message
                if (response == 'ok') {
                    let login = $('#login').val();
                    $('#userLogin').text('hello, ' + login).css('color', 'white');
                    $('#navButtonSign').css("display", "none");
                    $('#navButtonLogOut').css("display", "");
                } else {
                    alert(response);
                }
            }
        });

    });
    $('#navButtonLogOut').on('click', function () {
        document.cookie = "token=; max-age=0";
        location.reload();
    });
});

function checkInput(book) {
    for (let key in book) {
        let value = $.trim(book[key]);
        if (value.length == 0) {
            return false;
        }
        else {
            book[key] = value;
        }
    }
    let regExpWord = /^([A-Za-z]+[\s]?[A-Za-z]+)+$/;
    let checkName = regExpWord.test(book.name);
    let checkGenre = regExpWord.test(book.genre);
    let regExpNumber = /\D/;
    let checkPages = regExpNumber.test(book.pages);
    let result = (checkName == true && checkGenre == true && checkPages == false) ? book : false;
    return result;
};

function notSignIn() {
    alert('You are not sign in');
    location.reload();
};
