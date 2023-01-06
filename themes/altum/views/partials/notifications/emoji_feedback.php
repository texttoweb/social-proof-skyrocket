<?php defined('ALTUMCODE') || die() ?>


<?php ob_start() ?>
<div role="dialog" class="altumcode-wrapper altumcode-wrapper-<?= $notification->settings->border_radius ?> <?= $notification->settings->shadow ? 'altumcode-wrapper-shadow' : null ?> <?= ($notification->settings->direction ?? 'ltr') == 'rtl' ? 'altumcode-rtl' : null ?> altumcode-emoji-feedback-wrapper" style='font-family: <?= $notification->settings->font ?? 'inherit' ?>!important;background-color: <?= $notification->settings->background_color ?>;border-width: <?= $notification->settings->border_width ?>px;border-color: <?= $notification->settings->border_color ?>;<?= $notification->settings->background_pattern_svg ? 'background-image: url("' . $notification->settings->background_pattern_svg . '")' : null ?>;'>
    <div class="altumcode-emoji-feedback-content">
        <div class="altumcode-emoji-feedback-header">
            <p class="altumcode-emoji-feedback-title" style="color: <?= $notification->settings->title_color ?>"><?= $notification->settings->title ?></p>

            <button class="altumcode-close" style="color: <?= $notification->settings->close_button_color ?>;">×</button>
        </div>

        <div class="altumcode-emoji-feedback-emojis">
            <?php if($notification->settings->show_angry): ?>
                <img src="<?= ASSETS_FULL_URL . 'images/emojis/angry.svg' ?>" class="altumcode-emoji-feedback-emoji" data-type="angry" loading="lazy" />
            <?php endif ?>

            <?php if($notification->settings->show_sad): ?>
                <img src="<?= ASSETS_FULL_URL . 'images/emojis/sad.svg' ?>" class="altumcode-emoji-feedback-emoji" data-type="sad" loading="lazy" />
            <?php endif ?>

            <?php if($notification->settings->show_neutral): ?>
                <img src="<?= ASSETS_FULL_URL . 'images/emojis/neutral.svg' ?>" class="altumcode-emoji-feedback-emoji" data-type="neutral" loading="lazy" />
            <?php endif ?>

            <?php if($notification->settings->show_happy): ?>
                <img src="<?= ASSETS_FULL_URL . 'images/emojis/happy.svg' ?>" class="altumcode-emoji-feedback-emoji" data-type="happy" loading="lazy" />
            <?php endif ?>

            <?php if($notification->settings->show_excited): ?>
                <img src="<?= ASSETS_FULL_URL . 'images/emojis/excited.svg' ?>" class="altumcode-emoji-feedback-emoji" data-type="excited" loading="lazy" />
            <?php endif ?>
        </div>

        <?php if($notification->settings->display_branding): ?>
            <?php if(isset($notification->branding, $notification->branding->name, $notification->branding->url) && !empty($notification->branding->name) && !empty($notification->branding->url)): ?>
                <a href="<?= $notification->branding->url ?>" class="altumcode-site"><?= $notification->branding->name ?></a>
            <?php else: ?>
                <a href="<?= url() ?>" class="altumcode-site"><?= settings()->notifications->branding ?></a>
            <?php endif ?>
        <?php endif ?>
    </div>
</div>
<?php $html = ob_get_clean() ?>


<?php ob_start() ?>
new AltumCodeManager({
    should_show: !localStorage.getItem('notification_<?= $notification->notification_id ?>_converted'),
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

        /* On click event to the button */
        let emojis = main_element.querySelectorAll('.altumcode-emoji-feedback-emoji');

        for(let emoji of emojis) {
            emoji.addEventListener('click', event => {

                /* Trigger the animation */
                emoji.className += ' altumcode-emoji-feedback-emoji-clicked';

                /* Get all the other emojis and remove them */
                let other_emojis = main_element.querySelectorAll('.altumcode-emoji-feedback-emoji:not(.altumcode-emoji-feedback-emoji-clicked)');
                for(let other_emoji of other_emojis) {
                    other_emoji.remove();
                }

                let notification_id = main_element.getAttribute('data-notification-id');
                let feedback = emoji.getAttribute('data-type');

                send_tracking_data({
                    notification_id: notification_id,
                    type: 'notification',
                    subtype: `feedback_emoji_${feedback}`
                });

                /* Make sure to let the browser know of the conversion so that it is not shown again */
                localStorage.setItem(`notification_${notification_id}_converted`, true);

                setTimeout(() => {
                    AltumCodeManager.remove_notification(main_element);

                    /* Redirect the user to thank you url if needed */
                    let thank_you_url = <?= json_encode($notification->settings->thank_you_url) ?>;

                    if(thank_you_url.trim() != '') {
                        setTimeout(() => {
                            window.location.href = thank_you_url;
                        }, 350);
                    }

                }, 950);

            });
        }


    }
});
<?php $javascript = ob_get_clean(); ?>

<?php return (object) ['html' => $html, 'javascript' => $javascript] ?>
