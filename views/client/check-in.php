<?php
require_once '../../helpers/SessionHelper.php';
SessionHelper::requireLogin('client');
require_once '../../config/config.php';
include_once '../shared/header.php';
?>

<body class="bg-gray-50">
    <div class="flex h-screen">
        <?php include '../shared/clientSidebar.php'; ?>

        <!-- Main Content -->
        <div class="flex-1 overflow-auto bg-background">
            <div class="p-6">
                <!-- Header -->
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h1 class="text-xl font-semibold tracking-tight">Check-in Management</h1>
                        <p class="text-sm text-muted-foreground">Process and monitor event check-ins</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <button onclick="openQRScanner()" 
                            class="btn-hover inline-flex items-center justify-center rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 bg-primary text-primary-foreground hover:bg-primary/90 h-9 px-4">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v-4m6 6v10m-6-6h4m-6 2h2"/>
                            </svg>
                            Scan QR
                        </button>
                        <button onclick="openManualCheckin()" 
                            class="btn-hover inline-flex items-center justify-center rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 border border-input bg-background hover:bg-accent hover:text-accent-foreground h-9 px-4">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                            Manual Check-in
                        </button>
                    </div>
                </div>

                <!-- Active Events Section -->
                <div class="rounded-lg border bg-card text-card-foreground mb-6">
                    <div class="flex items-center justify-between p-6 pb-4">
                        <div>
                            <h2 class="text-lg font-medium leading-none tracking-tight">Active Events</h2>
                            <p class="text-sm text-muted-foreground mt-1">Select an event for check-in</p>
                        </div>
                    </div>
                    <div class="p-6 pt-0">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            <?php
                            try {
                                $stmt = $conn->prepare("
                                    SELECT * FROM events 
                                    WHERE client_id = ? 
                                    AND event_date >= CURDATE() 
                                    AND event_date <= DATE_ADD(CURDATE(), INTERVAL 1 DAY)
                                    ORDER BY event_date ASC
                                ");
                                $stmt->execute([$_SESSION['client']['id']]);
                                
                                while ($event = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                    $title = htmlspecialchars($event['title']);
                                    $location = htmlspecialchars($event['location']);
                                    
                                    echo "
                                    <div class='border rounded-lg p-4 hover:bg-accent hover:text-accent-foreground transition-colors cursor-pointer event-card'
                                         data-event-id='{$event['id']}'
                                         onclick='selectEvent({$event['id']})'>
                                        <div class='font-medium'>{$title}</div>
                                        <div class='text-sm text-muted-foreground mt-1'>" . date('g:i A', strtotime($event['event_date'])) . "</div>
                                        <div class='text-sm text-muted-foreground'>{$location}</div>
                                    </div>";
                                }

                                if ($stmt->rowCount() === 0) {
                                    echo "<div class='col-span-3 text-center text-sm text-muted-foreground'>No active events found</div>";
                                }
                            } catch(PDOException $e) {
                                error_log("Error fetching events: " . $e->getMessage());
                                echo "<div class='col-span-3 text-center text-sm text-red-600'>Error loading events</div>";
                            }
                            ?>
                        </div>
                    </div>
                </div>

                <!-- Recent Check-ins Section -->
                <div class="rounded-lg border bg-card text-card-foreground">
                    <div class="flex items-center justify-between p-6 pb-4">
                        <div>
                            <h2 class="text-lg font-medium leading-none tracking-tight">Recent Check-ins</h2>
                            <p class="text-sm text-muted-foreground mt-1" id="selectedEventTitle">Select an event to view check-ins</p>
                        </div>
                    </div>
                    <div class="p-6 pt-0">
                        <div class="relative w-full overflow-auto">
                            <table id="checkinsTable" class="w-full caption-bottom text-sm hidden">
                                <thead class="[&_tr]:border-b">
                                    <tr class="border-b transition-colors hover:bg-muted/50 data-[state=selected]:bg-muted">
                                        <th class="h-10 px-4 text-left align-middle font-medium text-muted-foreground">Participant</th>
                                        <th class="h-10 px-4 text-left align-middle font-medium text-muted-foreground">Check-in Time</th>
                                        <th class="h-10 px-4 text-left align-middle font-medium text-muted-foreground">Status</th>
                                    </tr>
                                </thead>
                                <tbody id="checkinsTableBody" class="[&_tr:last-child]:border-0">
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Manual Check-in Modal -->
    <div id="manualCheckinModal" class="fixed inset-0 bg-background/80 backdrop-blur-sm hidden z-50">
        <div class="fixed left-[50%] top-[50%] z-50 grid w-full max-w-lg translate-x-[-50%] translate-y-[-50%] gap-4 border bg-background p-6 shadow-lg duration-200 rounded-lg">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-semibold leading-none tracking-tight">Manual Check-in</h3>
                    <p class="text-sm text-muted-foreground mt-1">Enter participant registration code</p>
                </div>
                <button onclick="closeManualCheckin()" class="text-muted-foreground hover:text-foreground">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <form id="manualCheckinForm" onsubmit="submitManualCheckin(event)" class="space-y-4">
                <input type="hidden" id="eventSelect" name="event_id">
                <div>
                    <label class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70" for="registration_code">
                        Registration Code
                    </label>
                    <input type="text" id="registration_code" name="registration_code" required
                        class="flex h-9 w-full rounded-md border border-input bg-background px-3 py-1 text-sm shadow-sm transition-colors file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:cursor-not-allowed disabled:opacity-50"
                        placeholder="Enter registration code">
                </div>
                <div class="flex justify-end">
                    <button type="submit" 
                        class="btn-hover inline-flex items-center justify-center rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 bg-primary text-primary-foreground hover:bg-primary/90 h-9 px-4">
                        Check In
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- QR Scanner Modal -->
    <div id="qrScannerModal" class="fixed inset-0 bg-background/80 backdrop-blur-sm hidden z-50">
        <div class="fixed left-[50%] top-[50%] z-50 grid w-full max-w-lg translate-x-[-50%] translate-y-[-50%] gap-4 border bg-background p-6 shadow-lg duration-200 rounded-lg">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-semibold leading-none tracking-tight">Scan QR Code</h3>
                    <p class="text-sm text-muted-foreground mt-1">Scan participant's QR code</p>
                </div>
                <button onclick="closeQRScanner()" class="text-muted-foreground hover:text-foreground">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <div class="text-center">
                <div id="qr-reader" class="mb-4"></div>
                <div id="qr-reader-results"></div>
            </div>
        </div>
    </div>

    <script src="https://unpkg.com/html5-qrcode"></script>
    <script>
        let selectedEventId = null;
        let html5QrcodeScanner = null;

        function selectEvent(eventId) {
            selectedEventId = eventId;
            document.getElementById('checkinsTable').classList.remove('hidden');

            document.querySelectorAll('.event-card').forEach(card => {
                card.classList.remove('bg-accent', 'text-accent-foreground');
                if (card.dataset.eventId == eventId) {
                    card.classList.add('bg-accent', 'text-accent-foreground');
                    document.getElementById('selectedEventTitle').textContent = 
                        `Check-ins for ${card.querySelector('.font-medium').textContent}`;
                }
            });

            loadCheckins(eventId);
        }

        function loadCheckins(eventId) {
            fetch(`../../controllers/client/getRecentCheckins.php?event_id=${eventId}`)
                .then(response => response.json())
                .then(data => {
                    const tableBody = document.querySelector('#checkinsTableBody');
                    if (data.error) {
                        throw new Error(data.error);
                    }
                    
                    if (!data.checkins || data.checkins.length === 0) {
                        tableBody.innerHTML = `
                            <tr>
                                <td colspan="3" class="p-4 text-center text-sm text-muted-foreground">
                                    No check-ins found for this event
                                </td>
                            </tr>`;
                        return;
                    }

                    tableBody.innerHTML = data.checkins.map(checkin => `
                        <tr class="border-b transition-colors hover:bg-muted/50">
                            <td class="p-4 align-middle">${checkin.participant_name}</td>
                            <td class="p-4 align-middle">${new Date(checkin.checkin_time).toLocaleString()}</td>
                            <td class="p-4 align-middle">
                                <span class="inline-flex items-center rounded-md bg-green-50 px-2 py-1 text-xs font-medium text-green-700 ring-1 ring-inset ring-green-600/20">
                                    Checked In
                                </span>
                            </td>
                        </tr>
                    `).join('');
                })
                .catch(error => {
                    console.error('Error:', error);
                    document.querySelector('#checkinsTableBody').innerHTML = `
                        <tr>
                            <td colspan="3" class="p-4 text-center text-sm text-red-600">
                                Failed to load check-ins
                            </td>
                        </tr>`;
                });
        }

        function onScanSuccess(decodedText, decodedResult) {
            try {
                if (!selectedEventId) {
                    alert('Please select an event first');
                    return;
                }

                const data = JSON.parse(decodedText);
                // Submit the scanned registration code
                fetch('../../controllers/client/processCheckin.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        event_id: selectedEventId,
                        registration_code: data.registration_code
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Play success sound or show visual feedback
                        alert(data.message);
                        // Refresh check-ins table
                        loadCheckins(selectedEventId);
                        closeQRScanner();
                    } else {
                        alert(data.message || 'Check-in failed');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred during check-in');
                });
            } catch (e) {
                console.error("Invalid QR code format", e);
                alert('Invalid QR code format');
            }
        }

        function onScanFailure(error) {
            // Handle scan failure, usually better to ignore it.
            console.warn(`QR scan error = ${error}`);
        }

        function openQRScanner() {
            document.getElementById('qrScannerModal').classList.remove('hidden');
        }

        function closeQRScanner() {
            document.getElementById('qrScannerModal').classList.add('hidden');
        }

        function openManualCheckin() {
            document.getElementById('manualCheckinModal').classList.remove('hidden');
        }

        function closeManualCheckin() {
            document.getElementById('manualCheckinModal').classList.add('hidden');
        }

        function submitManualCheckin(event) {
            event.preventDefault();
            
            if (!selectedEventId) {
                alert('Please select an event first');
                return;
            }

            const registrationCode = document.getElementById('registration_code').value;
            
            fetch('../../controllers/client/processCheckin.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    event_id: selectedEventId,
                    registration_code: registrationCode
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Show success message
                    alert(data.message);
                    // Refresh check-ins table
                    loadCheckins(selectedEventId);
                    // Clear form and close modal
                    document.getElementById('registration_code').value = '';
                    closeManualCheckin();
                    closeQRScanner();
                } else {
                    alert(data.message || 'Check-in failed');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred during check-in');
            });
        }
    </script>
</body>
</html> 