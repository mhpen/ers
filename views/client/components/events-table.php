<table class="w-full caption-bottom text-sm">
    <thead>
        <tr class="border-b transition-colors">
            <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground [&:has([role=checkbox])]:pr-0">
                Event
            </th>
            <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground [&:has([role=checkbox])]:pr-0">
                Date
            </th>
            <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground [&:has([role=checkbox])]:pr-0">
                Price
            </th>
            <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground [&:has([role=checkbox])]:pr-0">
                Registrations
            </th>
            <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground [&:has([role=checkbox])]:pr-0">
                Status
            </th>
            <th class="h-12 px-4 text-right align-middle font-medium text-muted-foreground [&:has([role=checkbox])]:pr-0">
                Actions
            </th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($events)): ?>
        <tr class="border-b transition-colors hover:bg-muted/50">
            <td colspan="6" class="p-4 align-middle text-center text-muted-foreground">
                No events found. <a href="create-event.php" class="text-primary hover:underline">Create your first event</a>
            </td>
        </tr>
        <?php else: ?>
            <?php foreach ($events as $event): ?>
            <tr class="border-b transition-colors hover:bg-muted/50">
                <td class="p-4 align-middle [&:has([role=checkbox])]:pr-0">
                    <div class="flex items-center gap-4">
                        <div class="h-12 w-12 rounded-md border bg-muted">
                            <?php if ($event['banner']): ?>
                                <img src="../../public/<?php echo htmlspecialchars($event['banner']); ?>" 
                                     alt="" 
                                     class="aspect-square h-full w-full rounded-md object-cover">
                            <?php else: ?>
                                <div class="flex h-full items-center justify-center">
                                    <svg class="h-6 w-6 text-muted-foreground/50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="flex flex-col">
                            <span class="font-medium"><?php echo htmlspecialchars($event['title']); ?></span>
                            <span class="text-sm text-muted-foreground"><?php echo htmlspecialchars($event['location']); ?></span>
                        </div>
                    </div>
                </td>
                <td class="p-4 align-middle [&:has([role=checkbox])]:pr-0">
                    <div class="flex flex-col">
                        <span class="font-medium"><?php echo date('F j, Y', strtotime($event['event_date'])); ?></span>
                        <span class="text-sm text-muted-foreground"><?php echo date('g:i A', strtotime($event['event_date'])); ?></span>
                    </div>
                </td>
                <td class="p-4 align-middle [&:has([role=checkbox])]:pr-0">
                    <?php if ($event['price'] > 0): ?>
                        <div class="font-medium">$<?php echo number_format($event['price'], 2); ?></div>
                    <?php else: ?>
                        <span class="text-muted-foreground">Free</span>
                    <?php endif; ?>
                </td>
                <td class="p-4 align-middle [&:has([role=checkbox])]:pr-0">
                    <div class="flex items-center gap-2">
                        <div class="flex h-7 w-7 items-center justify-center rounded-lg border bg-muted">
                            <svg class="h-4 w-4 text-muted-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                        </div>
                        <span class="font-medium"><?php echo $event['registrations_count'] ?? 0; ?></span>
                    </div>
                </td>
                <td class="p-4 align-middle [&:has([role=checkbox])]:pr-0">
                    <div class="inline-flex items-center rounded-md border px-2.5 py-0.5 text-xs font-semibold transition-colors focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 <?php echo getStatusBadgeClasses($event['status']); ?>">
                        <?php echo ucfirst($event['status']); ?>
                    </div>
                </td>
                <td class="p-4 align-middle [&:has([role=checkbox])]:pr-0">
                    <div class="flex justify-end gap-2">
                        <a href="edit-event.php?id=<?php echo $event['id']; ?>" 
                            class="inline-flex items-center justify-center rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 border border-input hover:bg-accent hover:text-accent-foreground h-8 w-8 p-0"
                            title="Edit Event">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                        </a>
                        <a href="event-details.php?id=<?php echo $event['id']; ?>"
                            class="inline-flex items-center justify-center rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 border border-input hover:bg-accent hover:text-accent-foreground h-8 w-8 p-0"
                            title="View Details">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                        </a>
                        <a href="event-registrations.php?id=<?php echo $event['id']; ?>"
                            class="inline-flex items-center justify-center rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 border border-input hover:bg-accent hover:text-accent-foreground h-8 w-8 p-0"
                            title="View Registrations">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                        </a>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table> 