<!-- Ticket Modal -->
<div id="ticketModal" class="fixed inset-0 z-50 hidden">
    <!-- Backdrop -->
    <div class="fixed inset-0 bg-black/50 backdrop-blur-sm"></div>
    
    <!-- Modal Content -->
    <div class="fixed left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 w-full max-w-md">
        <div class="bg-white rounded-lg shadow-xl overflow-hidden">
            <!-- Modal Header -->
            <div class="bg-gradient-to-r from-purple-600 to-blue-600 px-6 py-4 flex justify-between items-center">
                <h3 class="text-xl font-bold text-white">Your Ticket</h3>
                <button onclick="closeTicketModal()" class="text-white hover:text-white/80">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            
            <!-- Modal Body -->
            <div class="p-6 space-y-6">
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <p class="text-gray-600">Movie</p>
                        <p class="font-semibold" id="ticketMovie"></p>
                    </div>
                    <div>
                        <p class="text-gray-600">Date</p>
                        <p class="font-semibold" id="ticketDate"></p>
                    </div>
                    <div>
                        <p class="text-gray-600">Time</p>
                        <p class="font-semibold" id="ticketTime"></p>
                    </div>
                    <div>
                        <p class="text-gray-600">Seat</p>
                        <p class="font-semibold" id="ticketSeat"></p>
                    </div>
                </div>
                
                <!-- Barcode -->
                <div class="text-center">
                    <div id="barcodeContainer" class="p-4 bg-white"></div>
                    <p class="mt-2 text-sm text-gray-600" id="ticketNumber"></p>
                </div>
                
                <!-- Actions -->
                <div class="flex justify-center gap-4 pt-4">
                    <button onclick="printTicket()" 
                            class="px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700">
                        Print Ticket
                    </button>
                    <button onclick="downloadTicket()" 
                            class="px-4 py-2 bg-gray-200 text-gray-800 text-sm rounded-lg hover:bg-gray-300">
                        Download
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add this script section -->
<script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>
<script>
    function showTicket(ticketData) {
        // Update modal content with ticket data
        document.getElementById('ticketMovie').textContent = ticketData.movieTitle;
        document.getElementById('ticketDate').textContent = ticketData.date;
        document.getElementById('ticketTime').textContent = ticketData.showtime;
        document.getElementById('ticketSeat').textContent = ticketData.seat;
        document.getElementById('ticketNumber').textContent = ticketData.ticketNumber;
        
        // Generate barcode
        JsBarcode("#barcodeCanvas", ticketData.ticketNumber, {
            format: "CODE128",
            width: 2,
            height: 100,
            displayValue: false
        });
        
        // Show modal
        document.getElementById('ticketModal').classList.remove('hidden');
    }
    
    function closeTicketModal() {
        document.getElementById('ticketModal').classList.add('hidden');
    }
    
    function printTicket() {
        window.print();
    }
    
    function downloadTicket() {
        // Implementation for downloading ticket as PDF or image
        // You might want to use a library like html2canvas or jsPDF
        alert('Download functionality to be implemented');
    }
    
    // Close modal when clicking outside
    document.getElementById('ticketModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeTicketModal();
        }
    });
</script>