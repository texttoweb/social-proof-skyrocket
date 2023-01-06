<?php defined('ALTUMCODE') || die() ?>


<?php ob_start() ?>
<?php $date_ended = new \DateTime($notification->settings->end_date) < new \DateTime() ?>
<?php $date = (new \DateTime($notification->settings->end_date))->diff(new \DateTime()) ?>
<div role="dialog" class="altumcode-wrapper altumcode-wrapper-<?= $notification->settings->border_radius ?> <?= $notification->settings->shadow ? 'altumcode-wrapper-shadow' : null ?> <?= ($notification->settings->direction ?? 'ltr') == 'rtl' ? 'altumcode-rtl' : null ?> altumcode-countdown-collector-wrapper" style='font-family: <?= $notification->settings->font ?? 'inherit' ?>!important;background-color: <?= $notification->settings->background_color ?>;border-width: <?= $notification->settings->border_width ?>px;border-color: <?= $notification->settings->border_color ?>;<?= $notification->settings->background_pattern_svg ? 'background-image: url("' . $notification->settings->background_pattern_svg . '")' : null ?>;'>
    <div class="altumcode-countdown-collector-content">
        <div class="altumcode-countdown-collector-header">
            <p class="altumcode-countdown-collector-title" style="color: <?= $notification->settings->title_color ?>"><?= $notification->settings->title ?></p>

            <button class="altumcode-close" style="color: <?= $notification->settings->close_button_color ?>;">×</button>
        </div>
        <p class="altumcode-countdown-collector-description" style="color: <?= $notification->settings->description_color ?>"><?= $notification->settings->description ?></p>

        <p class="altumcode-countdown-collector-content-title" style="color: <?= $notification->settings->content_title_color ?>"><?= $notification->settings->content_title ?></p>

        <div class="altumcode-countdown-collector-timer">
            <input type="hidden" name="end_date" value="<?= (new \DateTime($notification->settings->end_date))->getTimestamp() ?>" />

            <div class="altumcode-countdown-collector-timer-block">
                <div class="altumcode-countdown-collector-time" style="color: <?= $notification->settings->time_color ?>;background: <?= $notification->settings->time_background_color ?>" data-type="days"><?= $date_ended ? '0' : $date->format('%a') ?></div>
                <p class="altumcode-countdown-collector-time-text"><?= l('notification.countdown_collector.days') ?></p>
            </div>

            <div class="altumcode-countdown-collector-timer-block">
                <div class="altumcode-countdown-collector-time" style="color: <?= $notification->settings->time_color ?>;background: <?= $notification->settings->time_background_color ?>" data-type="hours"><?= $date_ended ? '0' : $date->format('%h') ?></div>
                <p class="altumcode-countdown-collector-time-text"><?= l('notification.countdown_collector.hours') ?></p>
            </div>

            <div class="altumcode-countdown-collector-timer-block">
                <div class="altumcode-countdown-collector-time" style="color: <?= $notification->settings->time_color ?>;background: <?= $notification->settings->time_background_color ?>" data-type="minutes"><?= $date_ended ? '0' : $date->format('%i') ?></div>
                <p class="altumcode-countdown-collector-time-text"><?= l('notification.countdown_collector.minutes') ?></p>
            </div>

            <div class="altumcode-countdown-collector-timer-block">
                <div class="altumcode-countdown-collector-time" style="color: <?= $notification->settings->time_color ?>;background: <?= $notification->settings->time_background_color ?>" data-type="seconds"><?= $date_ended ? '0' : $date->format('%s') ?></div>
                <p class="altumcode-countdown-collector-time-text"><?= l('notification.countdown_collector.seconds') ?></p>
            </div>
        </div>

        <div>
            <form class="altumcode-countdown-collector-form" id="altumcode-countdown-collector-form" name="" action="" method="POST" role="form">
                <div class="altumcode-countdown-collector-row">
                    <input type="text" class="" name="input" placeholder="<?= $notification->settings->input_placeholder ?>" aria-label="<?= $notification->settings->input_placeholder ?>" required="required" />

                    <button type="submit" name="button" style="color: <?= $notification->settings->button_color ?>; background: <?= $notification->settings->button_background_color ?>"><?= $notification->settings->button_text ?></button>
                </div>

                <?php if($notification->settings->show_agreement): ?>
                    <div class="altumcode-agreement-checkbox">
                        <input type="checkbox" id="agreement" class="altumcode-agreement-checkbox-input" name="agreement" required="required" />
                        <label for="agreement" class="altumcode-agreement-checkbox-text" style="color: <?= $notification->settings->description_color ?>">
                            <a href="<?= $notification->settings->agreement_url ?>" target="_blank">
                                <?= $notification->settings->agreement_text ?>
                            </a>
                        </label>
                    </div>
                <?php endif ?>
            </form>

            <?php if($notification->settings->display_branding): ?>
                <?php if(isset($notification->branding, $notification->branding->name, $notification->branding->url) && !empty($notification->branding->name) && !empty($notification->branding->url)): ?>
                    <a href="<?= $notification->branding->url ?>" class="altumcode-site"><?= $notification->branding->name ?></a>
                <?php else: ?>
                    <a href="<?= url() ?>" class="altumcode-site"><?= settings()->notifications->branding ?></a>
                <?php endif ?>
            <?php endif ?>
        </div>
    </div>
</div>
<?php $html = ob_get_clean() ?>


<?php ob_start() ?>
new AltumCodeManager({
    should_show: <?= json_encode(!$date_ended) ?>,
    content: <?= json_encode($html) ?>,
    display_mobile: <?= json_encode($notification->settings->display_mobile) ?>,
    display_desktop: <?= json_encode($notification->settings->display_desktop) ?>,
    display_trigger: <?= json_encode($notification->settings->display_trigger) ?>,
    display_trigger_value: <?= json_encode($notification->settings->display_trigger_value) ?>,
    duration: <?= $notification->settings->display_duration === -1 ? -1 : $notification->settings->display_duration * 1000 ?>,
    close: <?= json_encode($notification->settings->display_close_button) ?>,
    display_frequency: <?= json_encode($notification->settings->display_frequency) ?>,
    position: <?= json_encode($notification->settings->display_position) ?>,
    trigger_all_pages: <?= json_encode($notification->settings->trigger_all_pages) ?>,
    triggers: <?= json_encode($notification->settings->triggers) ?>,
    on_animation: <?= json_encode($notification->settings->on_animation) ?>,
    off_animation: <?= json_encode($notification->settings->off_animation) ?>,
    animation: <?= json_encode($notification->settings->animation) ?>,
    animation_interval: <?= (int) $notification->settings->animation_interval ?>,

    notification_id: <?= $notification->notification_id ?>
}).initiate({
    displayed: main_element => {

        /* Countdown */
        let end_date = new Date(main_element.querySelector('[name="end_date"]').value);

        let countdown = () => {
            let days_element = main_element.querySelector('[data-type="days"]');
            let hours_element = main_element.querySelector('[data-type="hours"]');
            let minutes_element = main_element.querySelector('[data-type="minutes"]');
            let seconds_element = main_element.querySelector('[data-type="seconds"]');

            let days = parseInt(days_element.innerText);
            let hours = parseInt(hours_element.innerText);
            let minutes = parseInt(minutes_element.innerText);
            let seconds = parseInt(seconds_element.innerText);

            let new_days = days;
            let new_hours = hours;
            let new_minutes = minutes;
            let new_seconds = seconds - 1;

            if(new_seconds < 0 && new_minutes > 0) {
                new_seconds = 60;
                new_minutes--;

                if(new_minutes < 0 && new_hours > 0) {
                    new_minutes = 60;
                    new_hours--;

                    if(new_hours < 0 && new_days > 0) {
                        new_hours = 60;
                        new_days--;

                        if(new_days < 0) {
                            new_days = 0;
                        }
                    }
                }
            }

            /* Check if the timer is up */
            if(days == 0 && hours == 0 && minutes == 0 && seconds == 0) {
                clearInterval(countdown_interval);

                AltumCodeManager.remove_notification(main_element);
            }

            /* Set the new values */
            days_element.innerText = new_days;
            hours_element.innerText = new_hours;
            minutes_element.innerText = new_minutes;
            seconds_element.innerText = new_seconds;
        };

        let countdown_interval = setInterval(countdown, 1000);

        /* Form submission */
        main_element.querySelector('#altumcode-countdown-collector-form').addEventListener('submit', event => {

            let input = event.currentTarget.querySelector('[name="input"]').value;
            let notification_id = main_element.getAttribute('data-notification-id');


            if(input.trim() != '') {

                /* Data collection from the form */
                send_tracking_data({
                    notification_id: notification_id,
                    type: 'collector',
                    input
                });

                AltumCodeManager.remove_notification(main_element);

                /* Make sure to let the browser know of the conversion so that it is not shown again */
                localStorage.setItem('notification_<?= $notification->notification_id ?>_converted', true);

                /* Redirect the user to thank you url if needed */
                let thank_you_url = <?= json_encode($notification->settings->thank_you_url) ?>;

                if(thank_you_url.trim() != '') {
                    setTimeout(() => {
                        window.location.href = thank_you_url;
                    }, 350);
                }

            }

            event.preventDefault();
        });

    }
});
<?php $javascript = ob_get_clean(); ?>

<?php return (object) ['html' => $html, 'javascript' => $javascript] ?>
