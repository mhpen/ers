<div class="min-h-[200px]">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php if (empty($events)): ?>
            <div class="col-span-full text-center p-8 border rounded-lg bg-card">
                <p class="text-muted-foreground">No events found. <a href="create-event.php" class="text-primary hover:underline">Create your first event</a></p>
            </div>
        <?php else: ?>
            <?php foreach ($events as $event): ?>
                <div class="rounded-lg border bg-card text-card-foreground shadow-sm overflow-hidden">
                    <!-- Event Banner -->
                    <div class="aspect-video w-full relative bg-muted">
                        <?php if ($event['banner']): ?>
                            <img src="../../public/<?php echo htmlspecialchars($event['banner']); ?>" 
                                 alt="<?php echo htmlspecialchars($event['title']); ?>"
                                 class="object-cover w-full h-full">
                        <?php else: ?>
                            <div class="flex items-center justify-center h-full">
                                <svg class="h-12 w-12 text-muted-foreground/50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                        <?php endif; ?>
                        
                        <!-- Status Badge -->
                        <div class="absolute top-2 right-2">
                            <div class="inline-flex items-center rounded-md border px-2.5 py-0.5 text-xs font-semibold transition-colors focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 <?php echo getStatusBadgeClasses($event['status']); ?>">
                                <?php echo ucfirst($event['status']); ?>
                            </div>
                        </div>
                    </div>

                    <!-- Event Details -->
                    <div class="p-6">
                        <div class="flex flex-col gap-4">
                            <div>
                                <h3 class="text-lg font-semibold"><?php echo htmlspecialchars($event['title']); ?></h3>
                                <p class="text-sm text-muted-foreground mt-1">
                                    <?php echo date('F j, Y • g:i A', strtotime($event['event_date'])); ?>
                                </p>
                            </div>
                            
                            <div class="flex items-center gap-2">
                                <div class="flex items-center gap-2">
                                    <div class="flex h-7 w-7 items-center justify-center rounded-lg border bg-muted">
                                        <svg class="h-4 w-4 text-muted-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                        </svg>
                                    </div>
                                    <span class="text-sm font-medium"><?php echo $event['registrations_count'] ?? 0; ?></span>
                                </div>
                                <?php if ($event['price'] > 0): ?>
                                    <div class="text-sm font-medium">$<?php echo number_format($event['price'], 2); ?></div>
                                <?php else: ?>
                                    <span class="text-sm text-muted-foreground">Free</span>
                                <?php endif; ?>
                            </div>

                            <div class="flex gap-2 mt-4">
                                <a href="edit-event.php?id=<?php echo $event['id']; ?>" 
                                   class="flex-1 inline-flex items-center justify-center rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 border border-input hover:bg-accent hover:text-accent-foreground h-9 px-4 py-2">
                                    Edit
                                </a>
                                <a href="event-details.php?id=<?php echo $event['id']; ?>"
                                   class="flex-1 inline-flex items-center justify-center rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 bg-primary text-primary-foreground hover:bg-primary/90 h-9 px-4 py-2">
                                    View
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div> 