<!DOCTYPE html>
<?php
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'notes';

// Create a database connection
$connection = mysqli_connect($host, $username, $password, $database);

// Check if the connection was successful
if (!$connection) {
    die("Database connection failed: " . mysqli_connect_error());
}
?>

<html>
<head>
<title>Current Date</title>
<link
    rel="stylesheet"
    href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css"
    integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm"
    crossorigin="anonymous"
/>
<link
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css"
    rel="stylesheet"
    integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ"
    crossorigin="anonymous"
/>

<link rel="stylesheet" href="style.css">

<style>
    .container {
    margin-top: 20px;
    }
    .notes {
    margin-top: 20px;
    }
</style>
</head>
<body>

<div class="loger">
    <button
    onclick="window.location.href='log.html'"
    class=" btn butno ml-2 mt-1"
    style="border: 1px solid grey"
    >
    Login
    </button>
    <button
    onclick="window.location.href='reg.html'"
    class=" btn butno ml-2 mt-1"
    style="border: 1px solid grey"
    >
    Register
    </button>
</div>
<div class="container">
    <div class="row justify-content-center">
    <div class="col-6">
        <h1 id="maintext" class="text-center">Current Date</h1>
        <p id="date" class="text-center"></p>
        <div class="text-center">
        <button
            style="border: 1px solid grey"
            class="btn mr-2 butno mt-2"
            id="decreaseDate"
        >
            Decrease
        </button>
        <button
            style="border: 1px solid grey"
            class="btn butno mt-2"
            id="increaseDate"
        >
            Increase
        </button>
        </div>
    </div>
    </div>
    <div class="row justify-content-center">
    <div class="col-6">
        <h2 class="text-center mt-4">Notes</h2>
        <div id="notesList" class="notes"></div>
        <form id="addNoteForm" class="mt-3">
        <div class="form-group">
            <input
            type="text"
            id="noteInput"
            class="form-control"
            rows="3"
            placeholder="Enter a note"
            ></input>
        </div>
        <div id="noteRequirements" style="color: red; display: none;">
            <ul>
            <li>Note must be shorter than 60 characters</li>
            </ul>
        </div>
        <div id="note0Requirements" style="color: red; display: none;">
            <ul>
            <li>Note must be at least 3 characters</li>
            </ul>
        </div>
        <button
            type="submit"
            style="border: 1px solid grey"
            class="btn butno"
        >
            Add Note
        </button>
        </form>
    </div>
    </div>
</div>

<script>
    var currentDate = new Date();
    var dateElement = document.getElementById("date");
    var maintextElement = document.getElementById("maintext");
    var decreaseButton = document.getElementById("decreaseDate");
    var increaseButton = document.getElementById("increaseDate");
    var addNoteForm = document.getElementById("addNoteForm");
    var noteInput = document.getElementById("noteInput");
    var notesList = document.getElementById("notesList");
    var notes = [];

    function decreaseDate() {
    currentDate.setDate(currentDate.getDate() - 1);
    updateDate();
    }

    function increaseDate() {
    currentDate.setDate(currentDate.getDate() + 1);
    updateDate();
    }

    function updateDate() {
    var formattedDate = currentDate.toLocaleDateString("en-US", {
        year: "numeric",
        month: "long",
        day: "numeric",
    });
    
    dateElement.textContent = formattedDate;

    if (currentDate.toDateString() === new Date().toDateString()) {
        maintextElement.style.display = "block";
    } else {
        maintextElement.style.display = "none";
    }

    renderNotes();
    }

    function addNote() {
        var note = noteInput.value.trim();
        if (note.length < 3) {
            var noteRequirements = document.getElementById("note0Requirements");
            noteRequirements.style.display = "block";
            return;
        } else if (note.length > 60) {
            var noteRequirements = document.getElementById("noteRequirements");
            noteRequirements.style.display = "block";
            return;
        }

        var timestamp = new Date().toLocaleTimeString("en-US", {
            hour: "2-digit",
            minute: "2-digit",
        });

        var noteObject = {
            date: currentDate.toDateString(),
            time: timestamp,
            note: note,
        };

        notes.push(noteObject);
        noteInput.value = "";
        renderNotes();
    }

    function removeNoteAndSave(index) {
    removeNote(index);
    saveNotesAsJSON();
    }

    function removeNote(index) {
    notes.splice(index, 1);
    renderNotes();
    }
    
    function renderNotes() {
        notesList.innerHTML = "";
        notes.forEach(function (noteObject, index) {
            if (noteObject.date === currentDate.toDateString()) {
                var noteItem = document.createElement("div");
                noteItem.className =
                    "alert  d-flex justify-content-between align-items-center";
                noteItem.innerHTML = `
                    <span>${noteObject.note} - ${noteObject.time}</span>
                    <button type="button" class="btn-close" aria-label="Remove" style="outline: none; box-shadow: none;" onclick="removeNoteAndSave(${index})"></button>
                `;
                notesList.appendChild(noteItem);
            }
        });
    }


    decreaseButton.addEventListener("click", decreaseDate);
    increaseButton.addEventListener("click", increaseDate);
    addNoteForm.addEventListener("submit", function (event) {
    event.preventDefault();
    addNote();
    saveNotesAsJSON();
    });

    function saveNotesAsJSON() {
    var notesJSON = JSON.stringify(notes);
    var blob = new Blob([notesJSON], { type: "application/json" });
    var downloadLink = document.createElement("a");
    downloadLink.href = URL.createObjectURL(blob);
    downloadLink.download = "notes.json";
    downloadLink.click();
    }

    function compareNotes() {
    fetch("notes.json")
        .then(function (response) {
        return response.json();
        })
        .then(function (data) {
        var currentDateNotes = data.filter(function (noteObject) {
            return noteObject.date === currentDate.toDateString();
        });

        // Check if any new notes are present in the JSON file
        currentDateNotes.forEach(function (noteObject) {
            if (!notes.find((note) => note.note === noteObject.note)) {
            var noteItem = document.createElement("div");
            noteItem.className =
                "alert  d-flex justify-content-between align-items-center";
            noteItem.innerHTML = `
                <span>${noteObject.note}</span>
                `;
            notesList.appendChild(noteItem);
            notes.push(noteObject);
            }
        });

        
    // Replace the existing compareNotes() function with the PHP code
    function compareNotes() {
        fetch("fetch_notes.php")
            .then(function (response) {
                return response.json();
            })
            .then(function (data) {
                // Use the retrieved notes from the database
                notes = data;

                // Render the updated notes
                renderNotes();
            })
            .catch(function (error) {
                console.log("Error loading notes:", error);
            });
    }

    // Run the comparison every 3 seconds
    setInterval(compareNotes, 3000);
    // Initialize the date when the page loads
    updateDate();


        // Check if any notes from the JSON file have been removed
        notes = notes.filter(function (note) {
            if (
            note.date === currentDate.toDateString() &&
            currentDateNotes.find((noteObject) => noteObject.note === note.note)
            ) {
            return true;
            }
            return false;
        });

        // Render the updated notes
        renderNotes();
        })
        .catch(function (error) {
        console.log("Error loading notes JSON:", error);
        });
    }

    // Run the comparison every 3 seconds
    setInterval(compareNotes, 3000);
    // Initialize the date when the page loads
    updateDate();
</script>

<script src="https://cdn.jsdelivr.net/npm/file-saver@2.0.5/FileSaver.min.js"></script>
<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KyZXEAg3QhqLMpG8r+Qv4jG6yZn+OW9wX/PHxX8BXvT5Qcjhmq5cKk0Hw0b6h0NE" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
</body>
</html>


