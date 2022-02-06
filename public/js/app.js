const SONGS_LIST = 'http://localhost:8080/api/songs'

document.addEventListener("DOMContentLoaded", refreshTable)

let order = 'id'
let direction = 'asc'
let with_filters = false

// Load all songs list
function refreshTable() {
    var req = new XMLHttpRequest()
    req.onreadystatechange = function() {
        if (req.readyState === 4) {
            // OK
            if (req.status === 200) {
                rewriteTable(this.response)
                return
            }

            // Validation exception
            if (req.status === 422) {
                report422(this.response)
                return
            }

            console.log('Unknown response status')
            console.log(req.status)
        }
    }

    let url = SONGS_LIST + "?order_by=" + order + "&order_direction=" + direction

    if (with_filters) {
        let td = document.getElementById('td').value
        if (parseInt(td) < 0 || isNaN(parseInt(td))) {
            with_filters = false
            alert('Total duration must be greater or equal 0')
            return 0
        }

        let tdc = document.getElementById('tdc').value
        url += "&total_duration=" + td + "&total_duration_condition=" + tdc
    }

    req.open('GET', url)
    req.send()
}

// Rewrite current user table with provided data
function rewriteTable(data) {
    let json = JSON.parse(data)
    let table = document.getElementById('users-table')
    let table_html = ''
    for(let i = 0; i < json.data.length; i++) {
        table_html += "<tr>\n"

        table_html += "<td>" + json.data[i].id + "</td>\n"
        table_html += "<td>" + json.data[i].name + "</td>\n"
        table_html += "<td>" + json.data[i].email + "</td>\n"
        table_html += "<td>" + json.data[i].duration + "</td>\n"
        table_html += "<td>" + json.data[i].total_duration + "</td>\n"

        table_html += "</tr>\n"
    }

    table.innerHTML = table_html
}

// Simple reporter
function report422(data) {
    console.log(data)
    alert(data)
}

function changeOrder() {
    let buf = event.target.innerHTML.toLowerCase()
    if (buf === order) {
        direction = (direction === 'asc') ? 'desc' : 'asc'
    }
    order = buf
    refreshTable()
}

function searchWithFilters() {
    with_filters = true
    refreshTable()
}

