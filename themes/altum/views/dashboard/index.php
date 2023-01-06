<?php defined('ALTUMCODE') || die() ?>

<header class="header">
    <div class="container">

        <div class="d-flex justify-content-between">
            <h1 class="h3"><span class="underline"><?= l('dashboard.header') ?></span></h1>
        </div>

        <div class="mt-4">
            <div class="row">
                <div class="col-12 col-sm-6 col-xl mb-4 position-relative">
                    <div class="card border-0 d-flex flex-row h-100 overflow-hidden" style="background: var(--body-bg)">
                        <div class="px-3 d-flex flex-column justify-content-center">
                            <i class="fa fa-fw fa-server text-primary-600"></i>
                        </div>

                        <div class="card-body text-truncate">
                            <?= sprintf(l('dashboard.total_campaigns'), '<span class="h6">' . nr($data->total_campaigns) . '</span>') ?>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-sm-6 col-xl mb-4 position-relative">
                    <div class="card border-0 d-flex flex-row h-100 overflow-hidden" style="background: var(--body-bg)">
                        <div class="px-3 d-flex flex-column justify-content-center">
                            <i class="fa fa-fw fa-bell text-primary-600"></i>
                        </div>

                        <div class="card-body text-truncate">
                            <?= sprintf(l('dashboard.total_notifications'), '<span class="h6">' . nr($data->total_notifications) . '</span>') ?>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-sm-6 col-xl mb-4 position-relative">
                    <div class="card border-0 d-flex flex-row h-100 overflow-hidden" data-toggle="tooltip" title="<?= l('global.date.this_month') ?>" style="background: var(--body-bg)">
                        <div class="px-3 d-flex flex-column justify-content-center">
                            <i class="fa fa-fw fa-adjust text-primary-600"></i>
                        </div>

                        <?php
                        $progress_percentage = $this->user->plan_settings->notifications_impressions_limit == '0' ? 100 : ($this->user->current_month_notifications_impressions / $this->user->plan_settings->notifications_impressions_limit) * 100;
                        $progress_class = $progress_percentage > 60 ? ($progress_percentage > 85 ? 'text-danger' : 'text-warning') : 'text-success';
                        ?>

                        <div class="card-body text-truncate">
                            <?= sprintf(l('dashboard.total_notifications_impressions'), '<span class="' . $progress_class .'">' . nr($this->user->current_month_notifications_impressions, 0, true) . '</span>', ($this->user->plan_settings->notifications_impressions_limit != -1 ? nr($this->user->plan_settings->notifications_impressions_limit, 0, true) : '∞')) ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</header>

<section class="container">

    <?= \Altum\Alerts::output_alerts() ?>

    <div class="mt-5 d-flex justify-content-between">
        <h2 class="h4"><?= l('dashboard.campaigns_header') ?></h2>

        <div class="col-auto p-0 d-flex">
            <div>
                <?php if($this->user->plan_settings->campaigns_limit != -1 && $data->total_campaigns >= $this->user->plan_settings->campaigns_limit): ?>
                    <button type="button" data-toggle="tooltip" title="<?= l('global.info_message.plan_feature_limit') ?>" class="btn btn-primary disabled">
                        <i class="fa fa-fw fa-sm fa-plus"></i> <?= l('campaigns.create') ?>
                    </button>
                <?php else: ?>
                    <button type="button" data-toggle="modal" data-target="#create_campaign_modal" class="btn btn-primary"><i class="fa fa-fw fa-sm fa-plus"></i> <?= l('campaigns.create') ?></button>
                <?php endif ?>
            </div>
        </div>
    </div>

    <?php if(count($data->campaigns)): ?>
        <div class="table-responsive table-custom-container mt-3">
            <table class="table table-custom">
                <thead>
                <tr>
                    <th><?= l('campaigns.table.campaign') ?></th>
                    <th class="d-none d-md-table-cell"></th>
                    <th><?= l('campaigns.table.is_enabled') ?></th>
                    <th></th>
                </tr>
                </thead>
                <tbody>

                <?php foreach($data->campaigns as $row): ?>
                    <?php $row->branding = json_decode($row->branding); ?>
                    <tr>
                        <td class="text-nowrap">
                            <div class="d-flex">
                                <img src="https://external-content.duckduckgo.com/ip3/<?= $row->domain ?>.ico" class="campaign-avatar rounded-circle mr-3" alt="" />

                                <div class="d-flex flex-column">
                                    <a href="<?= url('campaign/' . $row->campaign_id) ?>"><?= $row->name ?></a>

                                    <span class="text-muted">
                                        <?= $row->domain ?>
                                    </span>
                                </div>
                            </div>
                        </td>
                        <td class="text-nowrap d-none d-md-table-cell">
                            <span class="mr-2" data-toggle="tooltip" data-html="true" title="<?= sprintf(l('global.datetime_tooltip'), '<br />' . \Altum\Date::get($row->datetime, 2) . '<br /><small>' . \Altum\Date::get($row->datetime, 3) . '</small>') ?>">
                                <i class="fa fa-fw fa-calendar text-muted"></i>
                            </span>

                            <span class="mr-2" data-toggle="tooltip" data-html="true" title="<?= sprintf(l('global.last_datetime_tooltip'), ($row->last_datetime ? '<br />' . \Altum\Date::get($row->last_datetime, 2) . '<br /><small>' . \Altum\Date::get($row->last_datetime, 3) . '</small>' : '-')) ?>">
                                <i class="fa fa-fw fa-history text-muted"></i>
                            </span>
                        </td>
                        <td class="text-nowrap">
                            <div class="d-flex">
                                <div class="custom-control custom-switch" data-toggle="tooltip" title="<?= l('campaigns.table.is_enabled_tooltip') ?>">
                                    <input
                                            type="checkbox"
                                            class="custom-control-input"
                                            id="campaign_is_enabled_<?= $row->campaign_id ?>"
                                            data-row-id="<?= $row->campaign_id ?>"
                                            onchange="ajax_call_helper(event, 'campaigns-ajax', 'is_enabled_toggle')"
                                        <?= $row->is_enabled ? 'checked="checked"' : null ?>
                                    >
                                    <label class="custom-control-label clickable" for="campaign_is_enabled_<?= $row->campaign_id ?>"></label>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="d-flex justify-content-end">
                                <div class="dropdown">
                                <button type="button" class="btn btn-link text-secondary dropdown-toggle dropdown-toggle-simple" data-toggle="dropdown" data-boundary="viewport">
                                    <i class="fa fa-fw fa-ellipsis-v"></i>
                                </button>

                                <div class="dropdown-menu dropdown-menu-right">
                                    <a href="<?= url('campaign/' . $row->campaign_id) ?>" class="dropdown-item"><i class="fa fa-fw fa-sm fa-server mr-2"></i> <?= l('global.view') ?></a>
                                    <a href="<?= url('campaign/' . $row->campaign_id . '/statistics') ?>" class="dropdown-item"><i class="fa fa-fw fa-sm fa-chart-bar mr-2"></i> <?= l('campaign.statistics.link') ?></a>
                                    <a href="#" data-toggle="modal" data-target="#update_campaign" data-campaign-id="<?= $row->campaign_id ?>" data-name="<?= $row->name ?>" data-domain="<?= $row->domain ?>" data-include-subdomains="<?= (bool) $row->include_subdomains ?>" class="dropdown-item"><i class="fa fa-fw fa-sm fa-pencil-alt mr-2"></i> <?= l('global.edit') ?></a>

                                    <a
                                        href="#"
                                        data-toggle="modal"
                                        data-target="#campaign_pixel_key"
                                        data-pixel-key="<?= $row->pixel_key ?>"
                                        data-campaign-id="<?= $row->campaign_id ?>"
                                        class="dropdown-item"
                                    ><i class="fa fa-fw fa-sm fa-code mr-2"></i> <?= l('campaign.pixel_key') ?></a>

                                    <?php if($this->user->plan_settings->custom_branding): ?>
                                        <a href="#" data-toggle="modal" data-target="#custom_branding_campaign" data-campaign-id="<?= $row->campaign_id ?>" data-branding-name="<?= $row->branding->name ?? '' ?>" data-branding-url="<?= $row->branding->url ?? '' ?>" class="dropdown-item"><i class="fa fa-fw fa-sm fa-random mr-2"></i> <?= l('campaign.custom_branding') ?></a>
                                    <?php endif ?>
                                    <a href="#" data-toggle="modal" data-target="#campaign_duplicate_modal" data-campaign-id="<?= $row->campaign_id ?>" class="dropdown-item"><i class="fa fa-fw fa-sm fa-copy mr-2"></i> <?= l('global.duplicate') ?></a>

                                    <a href="#" data-toggle="modal" data-target="#campaign_delete_modal" data-campaign-id="<?= $row->campaign_id ?>" data-resource-name="<?= $row->name ?>" class="dropdown-item"><i class="fa fa-fw fa-sm fa-trash-alt mr-2"></i> <?= l('global.delete') ?></a>
                                </div>
                            </div>
                            </div>
                        </td>
                    </tr>
                <?php endforeach ?>

                    <tr>
                        <td class="py-3" colspan="5">
                            <a href="<?= url('campaigns') ?>" class="text-muted">
                                <i class="fa fa-angle-right fa-sm fa-fw mr-1"></i> <?= l('dashboard.view_all') ?>
                            </a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

    <?php else: ?>

        <div class="d-flex flex-column align-items-center justify-content-center py-3">
            <img src="<?= ASSETS_FULL_URL . 'images/no_rows.svg' ?>" class="col-10 col-md-7 col-lg-4 mb-3" alt="<?= l('global.no_data') ?>" />
            <h2 class="h4 text-muted"><?= l('global.no_data') ?></h2>
            <p class="text-muted"><?= l('campaigns.no_data') ?></a></p>
        </div>

    <?php endif ?>


    <?php if(count($data->notifications)): ?>
        <div class="mt-5 d-flex justify-content-between">
            <h2 class="h4"><?= l('dashboard.notifications_header') ?></h2>
        </div>

        <div class="table-responsive table-custom-container mt-3">
            <table class="table table-custom">
                <thead>
                    <tr>
                        <th><?= l('notifications.table.name') ?></th>
                        <th class="d-none d-md-table-cell"><?= l('notifications.table.display_trigger') ?></th>
                        <th class="d-none d-md-table-cell"><?= l('notifications.table.display_duration') ?></th>
                        <th><?= l('notifications.table.is_enabled') ?></th>
                        <th></th>
                    </tr>
                    </thead>
                <tbody>
                <?php foreach($data->notifications as $row): ?>
                    <?php $row->settings = json_decode($row->settings) ?>

                    <tr>
                        <td class="text-nowrap">
                            <div class="d-flex flex-column">
                                <a href="<?= url('notification/' . $row->notification_id) ?>"><?= $row->name ?></a>

                                <div class="text-muted">
                                    <i class="<?= l('notification.' . mb_strtolower($row->type) . '.icon') ?> fa-sm mr-1"></i> <?= l('notification.' . mb_strtolower($row->type) . '.name') ?>
                                </div>
                            </div>
                        </td>
                        <td class="text-nowrap d-none d-md-table-cell">
                            <div class="text-muted d-flex flex-column">

                                <?php
                                switch($row->settings->display_trigger) {
                                    case 'delay':

                                        echo '<span>' . $row->settings->display_trigger_value . ' <small>' . l('global.date.seconds') . '</small></span>';
                                        echo '<small>' . l('notification.settings.display_trigger_' . $row->settings->display_trigger) . '</small>';

                                        break;

                                    case 'scroll':

                                        echo $row->settings->display_trigger_value . '%';
                                        echo '<small>' . l('notification.settings.display_trigger_' . $row->settings->display_trigger)  . '</small>';

                                        break;

                                    case 'exit_intent':

                                        echo l('notification.settings.display_trigger_' . $row->settings->display_trigger);

                                        break;
                                }
                                ?>

                            </div>
                        </td>
                        <td class="text-nowrap d-none d-md-table-cell">
                            <span><?= $row->settings->display_duration == -1 ? l('notifications.table.display_duration_unlimited') : $row->settings->display_duration . ' <small>' . l('global.date.seconds') . '</small>' ?></span>
                        </td>
                        <td class="text-nowrap">
                            <div class="d-flex">
                                <div class="custom-control custom-switch" data-toggle="tooltip" title="<?= l('notifications.table.is_enabled_tooltip') ?>">
                                    <input
                                            type="checkbox"
                                            class="custom-control-input"
                                            id="notification_is_enabled_<?= $row->notification_id ?>"
                                            data-row-id="<?= $row->notification_id ?>"
                                            onchange="ajax_call_helper(event, 'notifications-ajax', 'is_enabled_toggle')"
                                        <?= $row->is_enabled ? 'checked="checked"' : null ?>
                                    >
                                    <label class="custom-control-label clickable" for="notification_is_enabled_<?= $row->notification_id ?>"></label>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="d-flex justify-content-end">
                                <div class="dropdown">
                                    <button type="button" class="btn btn-link text-secondary dropdown-toggle dropdown-toggle-simple" data-toggle="dropdown" data-boundary="viewport">
                                        <i class="fa fa-fw fa-ellipsis-v"></i>
                                    </button>

                                    <div class="dropdown-menu dropdown-menu-right">
                                        <a href="<?= url('notification/' . $row->notification_id) ?>" class="dropdown-item"><i class="fa fa-fw fa-sm fa-pencil-alt mr-2"></i> <?= l('global.edit') ?></a>
                                        <a href="<?= url('notification/' . $row->notification_id . '/statistics') ?>" class="dropdown-item"><i class="fa fa-fw fa-sm fa-chart-bar mr-2"></i> <?= l('notification.statistics.link') ?></a>
                                        <a href="#" data-toggle="modal" data-target="#notification_duplicate_modal" data-notification-id="<?= $row->notification_id ?>" class="dropdown-item"><i class="fa fa-fw fa-sm fa-copy mr-2"></i> <?= l('global.duplicate') ?></a>
                                        <a href="#" data-toggle="modal" data-target="#notification_delete_modal" data-notification-id="<?= $row->notification_id ?>" data-resource-name="<?= $row->name ?>" class="dropdown-item"><i class="fa fa-fw fa-sm fa-trash-alt mr-2"></i> <?= l('global.delete') ?></a>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                <?php endforeach ?>
                </tbody>
            </table>
        </div>
    <?php endif ?>
</section>

<?php \Altum\Event::add_content(include_view(THEME_PATH . 'views/campaign/create_campaign_modal.php'), 'modals'); ?>
<?php \Altum\Event::add_content(include_view(THEME_PATH . 'views/campaign/campaign_pixel_key_modal.php'), 'modals'); ?>
<?php \Altum\Event::add_content(include_view(THEME_PATH . 'views/campaign/update_campaign_modal.php'), 'modals'); ?>
<?php \Altum\Event::add_content(include_view(THEME_PATH . 'views/campaign/custom_branding_campaign_modal.php'), 'modals'); ?>
<?php \Altum\Event::add_content(include_view(THEME_PATH . 'views/partials/duplicate_modal.php', ['modal_id' => 'campaign_duplicate_modal', 'resource_id' => 'campaign_id', 'path' => 'campaign/duplicate']), 'modals'); ?>
<?php \Altum\Event::add_content(include_view(THEME_PATH . 'views/partials/duplicate_modal.php', ['modal_id' => 'notification_duplicate_modal', 'resource_id' => 'notification_id', 'path' => 'notification/duplicate']), 'modals'); ?>
<?php \Altum\Event::add_content(include_view(THEME_PATH . 'views/partials/universal_delete_modal_form.php', [
    'name' => 'campaign',
    'resource_id' => 'campaign_id',
    'has_dynamic_resource_name' => true,
    'path' => 'campaign/delete'
]), 'modals'); ?>
<?php \Altum\Event::add_content(include_view(THEME_PATH . 'views/partials/universal_delete_modal_form.php', [
    'name' => 'notification',
    'resource_id' => 'notification_id',
    'has_dynamic_resource_name' => true,
    'path' => 'notification/delete'
]), 'modals'); ?>
