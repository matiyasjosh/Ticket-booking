<!-- ticket_modal.php -->
<div id="ticketModal" class="fixed inset-0 flex items-center justify-center bg-black/70 backdrop-blur-sm hidden">
    <div class="bg-white text-black rounded-xl shadow-lg w-full max-w-md p-6 relative">
        <!-- Close Button -->
        <button id="closeModal" class="absolute top-4 right-4 text-gray-500 hover:text-gray-800">&times;</button>

        <!-- Step 1: Ticket Selection -->
        <div id="step1">
            <h2 class="text-2xl font-bold text-center mb-4">Select Tickets</h2>
            
            <!-- Ticket Quantity -->
            <label class="block text-gray-700 mb-2">Number of Tickets:</label>
            <input type="number" id="ticketCount" min="1" max="5" value="1" class="w-full p-2 border rounded mb-4" />

            <!-- Seat Selection -->
            <div class="text-gray-700 mb-2">Choose Seats:</div>
            <div id="seatGrid" class="grid grid-cols-4 gap-2">
                <!-- Seats will be generated dynamically -->
            </div>

            <!-- Continue Button -->
            <button id="continueBtn"
                class="w-full py-3 mt-4 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition">
                Continue
            </button>
        </div>

        <!-- Step 2: Ticket Confirmation -->
        <div id="step2" class="hidden">
            <h2 class="text-2xl font-bold text-center mb-4">Your Ticket</h2>

            <!-- QR Code -->
            <div class="flex justify-center mb-4">
                <canvas id="qrCodeCanvas" class="w-32 h-32"></canvas>
            </div>

            <!-- Ticket Details -->
            <div class="space-y-3 text-sm text-gray-700">
                <p><strong>Movie:</strong> <span id="movieTitle"></span></p>
                <p><strong>Seats:</strong> <span id="selectedSeats"></span></p>
                <p><strong>Showtime:</strong> <span id="showTime"></span></p>
                <p><strong>Total Price:</strong> <span id="ticketPrice"> Birr</span></p>
            </div>

            <!-- Confirm Button -->
            <button id="confirmTicket"
                class="w-full py-3 mt-4 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700 transition">
                Confirm & Close
            </button>
        </div>
    </div>
</div>
