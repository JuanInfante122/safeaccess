<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calendario Anual</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f8f9fa;
            color: #333;
        }

        h1 {
            text-align: center;
            color: #444;
            margin-bottom: 20px;
            animation: fadeIn 1s ease;
        }

        #calendar-container {
            max-width: 800px;
            margin: 0 auto;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
            animation: slideIn 1s ease;
        }

        .calendar-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .calendar-header button {
            background: #495057;
            color: #fff;
            border: none;
            border-radius: 5px;
            padding: 10px 15px;
            font-size: 16px;
            cursor: pointer;
            transition: background 0.3s, transform 0.3s;
        }

        .calendar-header button:hover {
            background: #343a40;
            transform: scale(1.1);
        }

        .month-title {
            font-size: 18px;
            font-weight: 600;
            text-align: center;
        }

        .calendar {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 10px;
        }

        .day {
            background: #f1f1f1;
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 10px;
            text-align: center;
            cursor: pointer;
            transition: background 0.3s, transform 0.3s;
            animation: fadeIn 0.5s ease;
        }

        .day:hover {
            background: #e2e6ea;
            border-color: #adb5bd;
            transform: scale(1.1);
        }

        .selected {
            border: 2px solid #495057;
            background: #e9ecef;
            animation: pulse 1s infinite;
        }

        #note-section {
            display: none;
            margin-top: 20px;
            background: #f9f9f9;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            animation: slideDown 0.5s ease;
        }

        #note-input, #color-input, #time-input {
            width: 100%;
            margin-top: 10px;
            padding: 10px;
            border: 1px solid #ced4da;
            border-radius: 5px;
            transition: border-color 0.3s;
        }

        #note-input:focus, #color-input:focus, #time-input:focus {
            border-color: #495057;
        }

        .btn-add-note {
            display: block;
            width: 100%;
            margin-top: 10px;
            padding: 10px;
            background: #495057;
            color: #fff;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            transition: background 0.3s, transform 0.3s;
        }

        .btn-add-note:hover {
            background: #343a40;
            transform: scale(1.1);
        }

        .note-container h3 {
            margin-bottom: 10px;
            font-size: 16px;
            color: #495057;
        }

        .note {
            margin-top: 10px;
            background: #e9ecef;
            border: 1px solid #ced4da;
            border-radius: 5px;
            padding: 5px;
            font-size: 14px;
            color: #333;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }

        @keyframes slideIn {
            from {
                transform: translateY(-20px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        @keyframes slideDown {
            from {
                transform: translateY(-10px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        @keyframes pulse {
            0%, 100% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.05);
            }
        }
    </style>
</head>
<body>
    <div id="calendar-container">
        <div class="calendar-header">
            <button id="prev-month">&larr; Anterior</button>
            <div class="month-title" id="month-title">Enero</div>
            <button id="next-month">Siguiente &rarr;</button>
        </div>
        <div id="calendar" class="calendar"></div>
        <div id="note-section">
            <textarea id="note-input" placeholder="Escribe una nota para el día seleccionado..."></textarea>
            <input type="color" id="color-input" value="#f1f1f1">
            <input type="time" id="time-input">
            <button id="add-note-btn" class="btn-add-note">Agregar Nota</button>
            <div id="note-container" class="note-container">
                <h3>Notas del Día Seleccionado</h3>
                <div id="notes"></div>
            </div>
        </div>
    </div>

    <script>
        function initCalendar() {
            const calendar = document.getElementById('calendar');
            const monthTitle = document.getElementById('month-title');
            const prevMonthBtn = document.getElementById('prev-month');
            const nextMonthBtn = document.getElementById('next-month');
            const noteSection = document.getElementById('note-section');
            const noteInput = document.getElementById('note-input');
            const colorInput = document.getElementById('color-input');
            const timeInput = document.getElementById('time-input');
            const notesContainer = document.getElementById('notes');
            const addNoteBtn = document.getElementById('add-note-btn');
            let selectedDay = null;
            const notes = {};
            let currentMonthIndex = 0;

            const months = [
                "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio",
                "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"
            ];
            const daysInMonth = [31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];

            function renderCalendar(monthIndex) {
                calendar.innerHTML = '';
                monthTitle.textContent = months[monthIndex];

                for (let day = 1; day <= daysInMonth[monthIndex]; day++) {
                    const dayDiv = document.createElement('div');
                    dayDiv.classList.add('day');
                    dayDiv.textContent = day;
                    dayDiv.dataset.date = `${day}/${monthIndex + 1}`;
                    dayDiv.addEventListener('click', () => selectDay(dayDiv));
                    calendar.appendChild(dayDiv);
                }
            }

            function selectDay(day) {
                if (selectedDay) {
                    selectedDay.classList.remove('selected');
                }
                selectedDay = day;
                selectedDay.classList.add('selected');
                noteSection.style.display = 'block';
                displayNotes(selectedDay.dataset.date);
            }

            function addNote() {
                if (!selectedDay) {
                    alert('Por favor selecciona un día.');
                    return;
                }

                const noteText = noteInput.value.trim();
                const time = timeInput.value.trim();
                const color = colorInput.value;

                if (noteText === '' || time === '') {
                    alert('Por favor completa todos los campos.');
                    return;
                }

                const date = selectedDay.dataset.date;

                if (!notes[date]) {
                    notes[date] = [];
                }

                notes[date].push(`${time} - ${noteText}`);
                selectedDay.style.backgroundColor = color;
                noteInput.value = '';
                timeInput.value = '';

                displayNotes(date);
            }

            function displayNotes(date) {
                notesContainer.innerHTML = '';
                if (notes[date]) {
                    notes[date].forEach((note) => {
                        const noteDiv = document.createElement('div');
                        noteDiv.classList.add('note');
                        noteDiv.textContent = note;
                        notesContainer.appendChild(noteDiv);
                    });
                }
            }

            prevMonthBtn.addEventListener('click', () => {
                currentMonthIndex = (currentMonthIndex - 1 + months.length) % months.length;
                renderCalendar(currentMonthIndex);
                noteSection.style.display = 'none';
            });

            nextMonthBtn.addEventListener('click', () => {
                currentMonthIndex = (currentMonthIndex + 1) % months.length;
                renderCalendar(currentMonthIndex);
                noteSection.style.display = 'none';
            });

            addNoteBtn.addEventListener('click', addNote);

            renderCalendar(currentMonthIndex);
        }

        initCalendar();
    </script>
</body>
</html>
