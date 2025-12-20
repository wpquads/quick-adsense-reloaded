<?php
/**
 * Admin Pages
 *
 * @package     QUADS
 * @subpackage  Admin/Pages
 * @copyright   Copyright (c) 2015, René Hermenau
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       2.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Creates the admin submenu pages under the Quick AdSense Reloaded menu and assigns their
 * links to global variables
 *
 * @since 1.0
 * @global $quads_settings_page
 * @global $quads_add_ons_page
 * @return void
 */
function quads_add_options_link() {
    global $quads_options, $quads_parent_page, $quads_add_ons_page, $quads_add_ons_page2,$quads_permissions, $quads_settings_page, $quads_mode;

    $quads_mode = get_option('quads-mode');

    $label = quads_is_extra() ? 'WP QUADS PRO' : 'WP QUADS';

    $create_settings = isset($quads_options['create_settings']) ? true : false;
    if ($create_settings && $quads_mode != 'new') {
        $quads_settings_page = add_submenu_page('options-general.php', esc_html__('QUADS Settings', 'quick-adsense-reloaded'), esc_html__('QUADS', 'quick-adsense-reloaded'),$quads_permissions, 'quads-settings', 'quads_options_page');
    } else {
        $quads_logo = "PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0idXRmLTgiPz4NCjxzdmcgdmVyc2lvbj0iMS4xIiB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHhtbG5zOnhsaW5rPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5L3hsaW5rIiB4PSIwcHgiIHk9IjBweCINCgkgd2lkdGg9IjYxMS42OHB4IiBoZWlnaHQ9IjU0NXB4IiB2aWV3Qm94PSIwIDAgNjExLjY4IDU0NSIgZW5hYmxlLWJhY2tncm91bmQ9Im5ldyAwIDAgNjExLjY4IDU0NSIgeG1sOnNwYWNlPSJwcmVzZXJ2ZSI+DQoJIDxzdHlsZT4uc3R5bGUwe2ZpbGw6I2ZmZjt9PC9zdHlsZT4NCjxnIGlkPSJFYmVuZV8wX3hBMF9JbWFnZV8xXyI+DQo8L2c+DQo8ZyBpZD0iV1BRVUFEUyI+DQoJPGc+DQoJCTxwYXRoIGNsYXNzPSJzdHlsZTAiIGQ9Ik0yNS43MDksNTM1LjQyTDMuMzIsNDUxLjA0NGgxMS40NTRsMTIuODM0LDU1LjMxMWMxLjM4MSw1Ljc5NCwyLjU3LDExLjU1LDMuNTY4LDE3LjI2Nw0KCQkJYzIuMTQ4LTkuMDE3LDMuNDE1LTE0LjIxNiwzLjc5OS0xNS41OThsMTYuMDU4LTU2Ljk3OWgxMy40NjhsMTIuMDg2LDQyLjcwNmMzLjAzMSwxMC41OSw1LjIxOCwyMC41NDcsNi41NjIsMjkuODcxDQoJCQljMS4wNzQtNS4zMzMsMi40NzUtMTEuNDUzLDQuMjAyLTE4LjM2bDEzLjIzNy01NC4yMTdoMTEuMjI0TDg4LjY3NSw1MzUuNDJINzcuOTEybC0xNy43ODQtNjQuMjg5DQoJCQljLTEuNDk3LTUuMzcyLTIuMzgtOC42NzEtMi42NDgtOS44OTljLTAuODgzLDMuODc2LTEuNzA4LDcuMTc1LTIuNDc1LDkuODk5bC0xNy45LDY0LjI4OUgyNS43MDl6Ii8+DQoJCTxwYXRoIGNsYXNzPSJzdHlsZTAiIGQ9Ik0xMjIuMjI5LDUzNS40MnYtODQuMzc2aDMxLjgyOGM1LjYwMiwwLDkuODc5LDAuMjY5LDEyLjgzNSwwLjgwNmM0LjE0NCwwLjY5LDcuNjE2LDIuMDA2LDEwLjQxNywzLjk0Mg0KCQkJYzIuOCwxLjkzOCw1LjA1NSw0LjY1Myw2Ljc2Myw4LjE0NWMxLjcwNywzLjQ5MiwyLjU2MSw3LjMyOSwyLjU2MSwxMS41MTFjMCw3LjE3Ni0yLjI4MywxMy4yNDgtNi44NDksMTguMjE3DQoJCQljLTQuNTY2LDQuOTY5LTEyLjgxNiw3LjQ1My0yNC43NDksNy40NTNoLTIxLjY0MXYzNC4zMDNIMTIyLjIyOXogTTEzMy4zOTUsNDkxLjE2aDIxLjgxM2M3LjIxMywwLDEyLjMzNi0xLjM0MywxNS4zNjctNC4wMjkNCgkJCWMzLjAzMS0yLjY4Niw0LjU0Ny02LjQ2NSw0LjU0Ny0xMS4zMzhjMC0zLjUzLTAuODkyLTYuNTUyLTIuNjc2LTkuMDY1Yy0xLjc4NC0yLjUxMy00LjEzNS00LjE3My03LjA1LTQuOTc5DQoJCQljLTEuODgtMC40OTgtNS4zNTMtMC43NDgtMTAuNDE3LTAuNzQ4aC0yMS41ODNWNDkxLjE2eiIvPg0KCQk8cGF0aCBjbGFzcz0ic3R5bGUwIiBkPSJNMjY4LjI0Nyw1MjQuNzE1YzQuMTgyLDIuOTkyLDguNzI5LDUuMzcyLDEzLjY0MSw3LjEzN2wtNi4yNzMsMTIuMDI5DQoJCQljLTIuNTcxLTAuNzY5LTUuMDg0LTEuODIzLTcuNTQtMy4xNjZjLTAuNTM4LTAuMjY5LTQuMzE3LTIuNzYzLTExLjMzOC03LjQ4MmMtNS41MjUsMi40MTgtMTEuNjQ2LDMuNjI2LTE4LjM2LDMuNjI2DQoJCQljLTEyLjk3LDAtMjMuMTI4LTMuODE3LTMwLjQ3Ni0xMS40NTNjLTcuMzQ4LTcuNjM1LTExLjAyMS0xOC4zNi0xMS4wMjEtMzIuMTc0YzAtMTMuNzc0LDMuNjg0LTI0LjQ4OSwxMS4wNS0zMi4xNDUNCgkJCWM3LjM2Ny03LjY1NCwxNy4zNjItMTEuNDgxLDI5Ljk4Ni0xMS40ODFjMTIuNTA4LDAsMjIuNDI3LDMuODI3LDI5Ljc1NiwxMS40ODFjNy4zMjgsNy42NTUsMTAuOTkzLDE4LjM3LDEwLjk5MywzMi4xNDUNCgkJCWMwLDcuMjkxLTEuMDE3LDEzLjY5OC0zLjA1LDE5LjIyNEMyNzQuMDc5LDUxNi42NzcsMjcxLjYyMyw1MjAuNzYzLDI2OC4yNDcsNTI0LjcxNXogTTI1NC41NDksNTE1LjEwMw0KCQkJYzIuMTg3LTIuNTY5LDMuODI3LTUuNjc4LDQuOTIxLTkuMzIzYzEuMDkzLTMuNjQ2LDEuNjQtNy44MjgsMS42NC0xMi41NDhjMC05Ljc0NS0yLjE0OS0xNy4wMjYtNi40NDYtMjEuODQyDQoJCQljLTQuMjk3LTQuODE0LTkuOTE5LTcuMjIzLTE2Ljg2My03LjIyM2MtNi45NDUsMC0xMi41NzYsMi40MTctMTYuODkzLDcuMjUyYy00LjMxNyw0LjgzNC02LjQ3NSwxMi4xMDUtNi40NzUsMjEuODEzDQoJCQljMCw5Ljg2MiwyLjE1OCwxNy4yMzgsNi40NzUsMjIuMTMxYzQuMzE2LDQuODkyLDkuNzc0LDcuMzM4LDE2LjM3NSw3LjMzOGMyLjQ1NSwwLDQuNzc3LTAuNDAzLDYuOTY0LTEuMjA5DQoJCQljLTMuNDUzLTIuMjY0LTYuOTY0LTQuMDI4LTEwLjUzMi01LjI5NWw0Ljc3Ny05LjcyN0MyNDQuMDkyLDUwOC4zODksMjQ5LjQ0NSw1MTEuMjY3LDI1NC41NDksNTE1LjEwM3oiLz4NCgkJPHBhdGggY2xhc3M9InN0eWxlMCIgZD0iTTI5MS45MDIsNDUxLjA0NGgxNy4wMzd2NDUuNjk5YzAsNy4yNTIsMC4yMSwxMS45NTIsMC42MzMsMTQuMTAxYzAuNzI5LDMuNDUzLDIuNDY2LDYuMjI2LDUuMjA5LDguMzE2DQoJCQljMi43NDMsMi4wOTIsNi40OTQsMy4xMzcsMTEuMjUzLDMuMTM3YzQuODM0LDAsOC40NzktMC45ODcsMTAuOTM2LTIuOTY0YzIuNDU1LTEuOTc2LDMuOTMzLTQuNDAyLDQuNDMyLTcuMjgNCgkJCWMwLjQ5OC0yLjg3OCwwLjc0OC03LjY1NSwwLjc0OC0xNC4zMzF2LTQ2LjY3OGgxNy4wMzZ2NDQuMzE3YzAsMTAuMTMtMC40NiwxNy4yODYtMS4zODEsMjEuNDY4DQoJCQljLTAuOTIxLDQuMTg0LTIuNjE5LDcuNzEzLTUuMDk0LDEwLjU5MWMtMi40NzYsMi44NzgtNS43ODQsNS4xNzEtOS45MjksNi44NzhjLTQuMTQ0LDEuNzA3LTkuNTU0LDIuNTYxLTE2LjIzLDIuNTYxDQoJCQljLTguMDU4LDAtMTQuMTY5LTAuOTMxLTE4LjMzMi0yLjc5MWMtNC4xNjQtMS44Ni03LjQ1My00LjI3OC05Ljg3MS03LjI1MmMtMi40MTctMi45NzMtNC4wMS02LjA5MS00Ljc3Ny05LjM1Mw0KCQkJYy0xLjExMy00LjgzNS0xLjY2OS0xMS45NzItMS42NjktMjEuNDExVjQ1MS4wNDR6Ii8+DQoJCTxwYXRoIGNsYXNzPSJzdHlsZTAiIGQ9Ik00NTMuMjMsNTM1LjQyaC0xOC41MzNsLTcuMzY3LTE5LjE2NmgtMzMuNzI4bC02Ljk2NCwxOS4xNjZoLTE4LjA3MmwzMi44NjQtODQuMzc2aDE4LjAxNUw0NTMuMjMsNTM1LjQyeg0KCQkJIE00MjEuODYyLDUwMi4wMzhsLTExLjYyNi0zMS4zMTFsLTExLjM5NiwzMS4zMTFINDIxLjg2MnoiLz4NCgkJPHBhdGggY2xhc3M9InN0eWxlMCIgZD0iTTQ2Mi4yMDksNDUxLjA0NGgzMS4xMzdjNy4wMjIsMCwxMi4zNzUsMC41MzgsMTYuMDU5LDEuNjExYzQuOTQ5LDEuNDU5LDkuMTg4LDQuMDQ5LDEyLjcyLDcuNzcxDQoJCQljMy41MjksMy43MjIsNi4yMTYsOC4yNzgsOC4wNTgsMTMuNjY5YzEuODQyLDUuMzkyLDIuNzYzLDEyLjAzOSwyLjc2MywxOS45NDNjMCw2Ljk0NS0wLjg2MywxMi45MzEtMi41OSwxNy45NTcNCgkJCWMtMi4xMTEsNi4xNC01LjEyMywxMS4xMDgtOS4wMzcsMTQuOTA2Yy0yLjk1NSwyLjg3OC02Ljk0NSw1LjEyMy0xMS45NzEsNi43MzRjLTMuNzYxLDEuMTg5LTguNzg3LDEuNzg0LTE1LjA4LDEuNzg0aC0zMi4wNTgNCgkJCVY0NTEuMDQ0eiBNNDc5LjI0NSw0NjUuMzE3djU1Ljg4N2gxMi43MmM0Ljc1NywwLDguMTkxLTAuMjY5LDEwLjMwMi0wLjgwNmMyLjc2My0wLjY5MSw1LjA1Ni0xLjg2MSw2Ljg3OC0zLjUxMg0KCQkJYzEuODIyLTEuNjQ4LDMuMzEtNC4zNjQsNC40NjEtOC4xNDRzMS43MjctOC45MywxLjcyNy0xNS40NTRjMC02LjUyMi0wLjU3NS0xMS41MjktMS43MjctMTUuMDIxDQoJCQljLTEuMTUxLTMuNDkxLTIuNzYzLTYuMjE2LTQuODM1LTguMTczYy0yLjA3MS0xLjk1Ny00LjctMy4yOC03Ljg4NS0zLjk3MmMtMi4zOC0wLjUzNi03LjA0Mi0wLjgwNi0xMy45ODYtMC44MDZINDc5LjI0NXoiLz4NCgkJPHBhdGggY2xhc3M9InN0eWxlMCIgZD0iTTU0My4wNzMsNTA3Ljk2NmwxNi41NzYtMS42MTFjMC45OTcsNS41NjQsMy4wMjEsOS42NSw2LjA3MiwxMi4yNmMzLjA1LDIuNjA5LDcuMTY1LDMuOTEzLDEyLjM0NiwzLjkxMw0KCQkJYzUuNDg2LDAsOS42Mi0xLjE2LDEyLjQwMy0zLjQ4MWMyLjc4MS0yLjMyMSw0LjE3Mi01LjAzNiw0LjE3Mi04LjE0NWMwLTEuOTk0LTAuNTg1LTMuNjkyLTEuNzU1LTUuMDk0DQoJCQljLTEuMTcxLTEuNC0zLjIxNC0yLjYxOC02LjEzLTMuNjU0Yy0xLjk5NS0wLjY5MS02LjU0Mi0xLjkxOS0xMy42NDEtMy42ODRjLTkuMTMyLTIuMjY0LTE1LjU0LTUuMDQ1LTE5LjIyNC04LjM0Ng0KCQkJYy01LjE4LTQuNjQzLTcuNzctMTAuMzAzLTcuNzctMTYuOTc5YzAtNC4yOTcsMS4yMTgtOC4zMTcsMy42NTQtMTIuMDU5YzIuNDM3LTMuNzQsNS45NDctNi41OSwxMC41MzMtOC41NDcNCgkJCWM0LjU4NC0xLjk1NywxMC4xMi0yLjkzNSwxNi42MDQtMi45MzVjMTAuNTksMCwxOC41NjIsMi4zMjEsMjMuOTE0LDYuOTY0czguMTYzLDEwLjg0LDguNDMyLDE4LjU5bC0xNy4wMzYsMC43NDkNCgkJCWMtMC43MjktNC4zMzYtMi4yOTMtNy40NTQtNC42OS05LjM1NGMtMi4zOTgtMS44OTktNS45OTYtMi44NDktMTAuNzkyLTIuODQ5Yy00Ljk0OSwwLTguODI1LDEuMDE4LTExLjYyNiwzLjA1MQ0KCQkJYy0xLjgwNCwxLjMwNS0yLjcwNSwzLjA1LTIuNzA1LDUuMjM3YzAsMS45OTUsMC44NDQsMy43MDMsMi41MzIsNS4xMjJjMi4xNDgsMS44MDUsNy4zNjcsMy42ODQsMTUuNjU1LDUuNjQxDQoJCQlzMTQuNDE4LDMuOTgxLDE4LjM4OSw2LjA3MmMzLjk3MiwyLjA5Miw3LjA3OSw0Ljk0OSw5LjMyNCw4LjU3NWMyLjI0NCwzLjYyNiwzLjM2Nyw4LjEwNiwzLjM2NywxMy40MzkNCgkJCWMwLDQuODM1LTEuMzQ0LDkuMzYyLTQuMDI5LDEzLjU4M2MtMi42ODcsNC4yMjItNi40ODQsNy4zNTgtMTEuMzk2LDkuNDFjLTQuOTEyLDIuMDUzLTExLjAzMiwzLjA3OS0xOC4zNiwzLjA3OQ0KCQkJYy0xMC42NjgsMC0xOC44NTktMi40NjUtMjQuNTc2LTcuMzk2QzU0Ny42MDEsNTI0LjU5MSw1NDQuMTg2LDUxNy40MDUsNTQzLjA3Myw1MDcuOTY2eiIvPg0KCTwvZz4NCjwvZz4NCjxnIGlkPSJFYmVuZV80X0tvcGllX3hBMF9JbWFnZV8xXyI+DQoJDQoJCTxpbWFnZSBvdmVyZmxvdz0idmlzaWJsZSIgd2lkdGg9IjcyIiBoZWlnaHQ9IjI4IiBpZD0iRWJlbmVfNF9Lb3BpZV94QTBfSW1hZ2UiIHhsaW5rOmhyZWY9ImRhdGE6aW1hZ2UvcG5nO2Jhc2U2NCxpVkJPUncwS0dnb0FBQUFOU1VoRVVnQUFBRWdBQUFBY0NBSUFBQUJOa0c3eEFBQUFDWEJJV1hNQUFBc1NBQUFMRWdIUzNYNzhBQUFBCkdYUkZXSFJUYjJaMGQyRnlaUUJCWkc5aVpTQkpiV0ZuWlZKbFlXUjVjY2xsUEFBQUFEWkpSRUZVZU5yc3p3RU5BQUFJQXlEZlAvVE4Kb1lNR3BPMThGREV4TVRFeE1URXhNVEV4TVRFeE1URXhNVEV4TWJHYlZnQUJCZ0J1WTFQSkhBbElWd0FBQUFCSlJVNUVya0pnZ2c9PSIgdHJhbnNmb3JtPSJtYXRyaXgoMSAwIDAgMSA0IDM5MikiPg0KCTwvaW1hZ2U+DQo8L2c+DQo8ZyBpZD0iRWJlbmVfM19Lb3BpZV82X3hBMF9JbWFnZV8xXyI+DQoJDQoJCTxpbWFnZSBvdmVyZmxvdz0idmlzaWJsZSIgd2lkdGg9Ijc2IiBoZWlnaHQ9IjEwOCIgaWQ9IkViZW5lXzNfS29waWVfNl94QTBfSW1hZ2UiIHhsaW5rOmhyZWY9ImRhdGE6aW1hZ2UvcG5nO2Jhc2U2NCxpVkJPUncwS0dnb0FBQUFOU1VoRVVnQUFBRXdBQUFCc0NBSUFBQUJQV2NOS0FBQUFDWEJJV1hNQUFBc1NBQUFMRWdIUzNYNzhBQUFBCkdYUkZXSFJUYjJaMGQyRnlaUUJCWkc5aVpTQkpiV0ZuWlZKbFlXUjVjY2xsUEFBQUFJQkpSRUZVZU5yc3p3RUJBREFJQXlDMWYrZTkKZ0FrOE5LQ1QxSFZUSDVDVWxKU1VsSlNVbEpTVWxKU1VsSlNVbEpTVWxKU1VsSlNVbEpTVWxKU1VsSlNVbEpTVWxKU1VsSlNVbEpTVQpsSlNVbEpTVWxKU1VsSlNVbEpTVWxKU1VsSlNVbEpTVWxKU1VsSlNVbEpTVWxKU1VsSlNVbEpTVWxKU1VsSlRjUFFFR0FPSGxBOVhxCjVpMFFBQUFBQUVsRlRrU3VRbUNDIiB0cmFuc2Zvcm09Im1hdHJpeCgxIDAgMCAxIDIxNSAzMTIpIj4NCgk8L2ltYWdlPg0KPC9nPg0KPGcgaWQ9IkViZW5lXzNfS29waWVfN194QTBfSW1hZ2VfMV8iPg0KCQ0KCQk8aW1hZ2Ugb3ZlcmZsb3c9InZpc2libGUiIHdpZHRoPSI4MCIgaGVpZ2h0PSIxNzIiIGlkPSJFYmVuZV8zX0tvcGllXzdfeEEwX0ltYWdlIiB4bGluazpocmVmPSJkYXRhOmltYWdlL3BuZztiYXNlNjQsaVZCT1J3MEtHZ29BQUFBTlNVaEVVZ0FBQUZBQUFBQ3NDQUlBQUFCakk3eVlBQUFBQ1hCSVdYTUFBQXNTQUFBTEVnSFMzWDc4QUFBQQpHWFJGV0hSVGIyWjBkMkZ5WlFCQlpHOWlaU0JKYldGblpWSmxZV1I1Y2NsbFBBQUFBTDVKUkVGVWVOcnN6d0VCQUFBRUF6RDA3M3c5CjJCcXNrOVFuVTg4SUN3c0xDd3NMQ3dzTEN3c0xDd3NMQ3dzTEN3c0xDd3NMQ3dzTEN3c0xDd3NMQ3dzTEN3c0xDd3NMQ3dzTEN3c0wKQ3dzTEN3c0xDd3NMQ3dzTEN3c0xDd3NMQ3dzTEN3c0xDd3NMQ3dzTEN3c0xDd3NMQ3dzTEN3c0xDd3NMQ3dzTEN3c0xDd3NMQ3dzTApDd3NMQ3dzTEN3c0xDd3NMQ3dzTEN3c0xDd3NMQ3dzTEN3c0xDd3NMQ3dzTEN3c0xDd3NMQ3dzTEN3c0xDd3NMQ3dzTEM5K3lBZ3dBClozMEVWVHVscE5RQUFBQUFTVVZPUks1Q1lJST0iIHRyYW5zZm9ybT0ibWF0cml4KDEgMCAwIDEgMzE5IDI0OCkiPg0KCTwvaW1hZ2U+DQo8L2c+DQo8ZyBpZD0iRWJlbmVfM19Lb3BpZV84X3hBMF9JbWFnZV8xXyI+DQoJDQoJCTxpbWFnZSBvdmVyZmxvdz0idmlzaWJsZSIgd2lkdGg9IjgwIiBoZWlnaHQ9IjcyIiBpZD0iRWJlbmVfM19Lb3BpZV84X3hBMF9JbWFnZSIgeGxpbms6aHJlZj0iZGF0YTppbWFnZS9wbmc7YmFzZTY0LGlWQk9SdzBLR2dvQUFBQU5TVWhFVWdBQUFGQUFBQUJJQ0FJQUFBRHU5dVVNQUFBQUNYQklXWE1BQUFzU0FBQUxFZ0hTM1g3OEFBQUEKR1hSRldIUlRiMlowZDJGeVpRQkJaRzlpWlNCSmJXRm5aVkpsWVdSNWNjbGxQQUFBQUdGSlJFRlVlTnJzenpFQkFBQUlBeURYUC9Rcwo0U2MwSUczbmt3Z0xDd3NMQ3dzTEN3c0xDd3NMQ3dzTEN3c0xDd3NMQ3dzTEN3c0xDd3NMQ3dzTEN3c0xDd3NMQ3dzTEN3c0xDd3NMCkN3c0xDd3NMQ3dzTEN3c0xDd3NMQzk5YkFRWUEveWJYY2Qzc1lVOEFBQUFBU1VWT1JLNUNZSUk9IiB0cmFuc2Zvcm09Im1hdHJpeCgxIDAgMCAxIDEwNiAzNDgpIj4NCgk8L2ltYWdlPg0KPC9nPg0KPGcgaWQ9IkViZW5lXzNfS29waWVfOV94QTBfSW1hZ2VfMV8iPg0KCQ0KCQk8aW1hZ2Ugb3ZlcmZsb3c9InZpc2libGUiIHdpZHRoPSI3MiIgaGVpZ2h0PSIyODAiIGlkPSJFYmVuZV8zX0tvcGllXzlfeEEwX0ltYWdlIiB4bGluazpocmVmPSJkYXRhOmltYWdlL3BuZztiYXNlNjQsaVZCT1J3MEtHZ29BQUFBTlNVaEVVZ0FBQUVnQUFBRVlDQUlBQUFCd2RpZFRBQUFBQ1hCSVdYTUFBQXNTQUFBTEVnSFMzWDc4QUFBQQpHWFJGV0hSVGIyWjBkMkZ5WlFCQlpHOWlaU0JKYldGblpWSmxZV1I1Y2NsbFBBQUFBUWxKUkVGVWVOcnN6d0VOQUFBSUF5QzFmK2ZiCjQ0TUdiSkpwZEZOS1RFeE1URXhNVEV4TVRFeE1URXhNVEV4TVRFeE1URXhNVEV4TVRFeE1URXhNVEV4TVRFeE1URXhNVEV4TVRFeE0KVEV4TVRFeE1URXhNVEV4TVRFeE1URXhNVEV4TVRFeE1URXhNVEV4TVRFeE1URXhNVEV4TVRFeE1URXhNVEV4TVRFeE1URXhNVEV4TQpURXhNVEV4TVRFeE1URXhNVEV4TVRFeE1URXhNVEV4TVRFeE1URXhNVEV4TVRFeE1URXhNVEV4TVRFeE1URXhNVEV4TVRFeE1URXhNClRFeE1URXhNVEV4TVRFeE1URXhNVEV4TVRFeE1URXhNVEV4TVRFeE1URXhNVEV4TVRFeE1URXhNVEV4TVRFeE1URXhNVEV4TVRFeE0KVEV4TVRFeE1URXhNVEV4TVRLelJDekFBK0YwRkxVdDk2RjhBQUFBQVNVVk9SSzVDWUlJPSIgdHJhbnNmb3JtPSJtYXRyaXgoMSAwIDAgMSA0MzAgMTQwKSI+DQoJPC9pbWFnZT4NCjwvZz4NCjxnIGlkPSJFYmVuZV8zX0tvcGllXzEwX3hBMF9JbWFnZV8xXyI+DQoJDQoJCTxpbWFnZSBvdmVyZmxvdz0idmlzaWJsZSIgd2lkdGg9IjcyIiBoZWlnaHQ9IjQyMCIgaWQ9IkViZW5lXzNfS29waWVfMTBfeEEwX0ltYWdlIiB4bGluazpocmVmPSJkYXRhOmltYWdlL3BuZztiYXNlNjQsaVZCT1J3MEtHZ29BQUFBTlNVaEVVZ0FBQUVnQUFBR2tDQUlBQUFBZGZ2UmRBQUFBQ1hCSVdYTUFBQXNTQUFBTEVnSFMzWDc4QUFBQQpHWFJGV0hSVGIyWjBkMkZ5WlFCQlpHOWlaU0JKYldGblpWSmxZV1I1Y2NsbFBBQUFBWDlKUkVGVWVOcnN6MEVSQUFBSUF5RFhQL1JzCjRjT0RCcVR0ZkJReE1URXhNVEV4TVRFeE1URXhNVEV4TVRFeE1URXhNVEV4TVRFeE1URXhNVEV4TVRFeE1URXhNVEV4TVRFeE1URXgKTVRFeE1URXhNVEV4TVRFeE1URXhNVEV4TVRFeE1URXhNVEV4TVRFeE1URXhNVEV4TVRFeE1URXhNVEV4TVRFeE1URXhNVEV4TVRFeApNVEV4TVRFeE1URXhNVEV4TVRFeE1URXhNVEV4TVRFeE1URXhNVEV4TVRFeE1URXhNVEV4TVRFeE1URXhNVEV4TVRFeE1URXhNVEV4Ck1URXhNVEV4TVRFeE1URXhNVEV4TVRFeE1URXhNVEV4TVRFeE1URXhNVEV4TVRFeE1URXhNVEV4TVRFeE1URXhNVEV4TVRFeE1URXgKTVRFeE1URXhNVEV4TVRFeE1URXhNVEV4TVRFeE1URXhNVEV4TVRFeE1URXhNVEV4TVRFeE1URXhNVEV4TVRFeE1URXhNVEV4TVRFeApNVEV4TVRFeE1URXhNVEV4TVRFeE1URXhNVEV4TVRFeE1URXhNVEV4TVRFeE1URXhNVEV4TVRFeE1URXhNVEV4TVRFeE1URXhNVEV4Ck1URXhNVEV4TVRFeE1URXhNVEV4TVRHeE95dkFBRTYvNlBYS29tdGZBQUFBQUVsRlRrU3VRbUNDIiB0cmFuc2Zvcm09Im1hdHJpeCgxIDAgMCAxIDUzNiAwKSI+DQoJPC9pbWFnZT4NCjwvZz4NCjwvc3ZnPg0K
";

        if($quads_mode == 'new'){

            $quads_parent_page = add_menu_page('Quick AdSense Reloaded Settings', $label, $quads_permissions, 'quads-settings', 'quads_options_page_new', 'data:image/svg+xml;base64,' . $quads_logo);

            $quads_settings_page = add_submenu_page('quads-settings', esc_html__('Ads', 'quick-adsense-reloaded'), 'Ads', $quads_permissions, 'quads-settings', 'quads_options_page_new');
            $quads_settings = get_option('quads_settings',[]);
            $sellable_ads = isset($quads_settings['sellable_ads']) ? $quads_settings['sellable_ads'] : 1 ;
            if(  $sellable_ads ){
                add_submenu_page('quads-settings', esc_html__('Adsell', 'quick-adsense-reloaded'), 'Sellable Ads', $quads_permissions, 'quads-settings&path=adsell', 'quads_options_page_new');
            }

	        if( defined('QUADS_PRO_VERSION') ){
                $license_alert = $days = '';
                $license_info = get_option( 'quads_wp_quads_pro_license_active' );
                if ( isset( $license_info ) ) {
                    $license_exp = isset( $license_info->expires ) ? gmdate('Y-m-d', strtotime( $license_info->expires) ) : '' ;
                    $license_info_lifetime = isset( $license_info->expires ) ? $license_info->expires : '' ;
                    $today = gmdate('Y-m-d');
                    $exp_date = $license_exp;
                    $date1 = date_create($today);
                    $date2 = date_create($exp_date);
                    $diff = date_diff($date1,$date2);
                    $days = $diff->format("%a");
                    if( $license_info_lifetime == 'lifetime' ){
                    $days = 'Lifetime';
                    if ($days == 'Lifetime') {
                    $expire_msg = " Your License is Valid for Lifetime ";
                    }
                    }
                    else if($today > $exp_date){
                    $days = -$days;
                    }
                $license_alert = isset($days) && $days!==0 && $days<=30 && $days!=='Lifetime' ? "<span class='wpquads_pro_icon dashicons dashicons-warning wpquads_pro_alert' style='color: #ffb229;left: 3px;position: relative;'></span>": "" ;
                }
                if(quads_has_setting_access()){
                    $quads_settings_page = add_submenu_page('quads-settings', esc_html__('Settings', 'quick-adsense-reloaded'), 'Settings'.$license_alert.'', $quads_permissions, 'quads-settings&path=settings', 'quads_options_page_new');
                }            
            }
            else{
                if(quads_has_setting_access()){
                    $quads_settings_page = add_submenu_page('quads-settings', esc_html__('Settings', 'quick-adsense-reloaded'), 'Settings', $quads_permissions, 'quads-settings&path=settings', 'quads_options_page_new');
                }
            }
            
            $quads_settings_page = add_submenu_page('quads-settings', esc_html__('Reports', 'quick-adsense-reloaded'), 'Reports', $quads_permissions, 'quads-settings&path=reports', 'quads_options_page_new');

           // add_submenu_page('quads-settings', esc_html__('Return to Classic view', 'quick-adsense-reloaded'), 'Return to Classic view', $quads_permissions, 'quads_switch_to_old', 'quads_version_switch');

        }else{
            $quads_parent_page = add_menu_page('Quick AdSense Reloaded Settings', $label, 'manage_options', 'quads-settings', 'quads_options_page', 'data:image/svg+xml;base64,' . $quads_logo);

            $quads_settings_page = add_submenu_page('quads-settings', esc_html__('Ad Settings', 'quick-adsense-reloaded'), 'Ad Settings', 'manage_options', 'quads-settings', 'quads_options_page');

            add_submenu_page('quads-settings', esc_html__('Switch to New Interface', 'quick-adsense-reloaded'), 'Switch to New Interface', 'manage_options', 'quads_switch_to_new', 'quads_version_switch');

        }

        if (quads_is_extra() || quads_is_advanced()) {

        }else{
            $quads_add_ons_page = add_submenu_page('quads-settings', esc_html__('Get Add-On', 'quick-adsense-reloaded'), 'Upgrade to PRO', 'manage_options', 'quads-addons', 'quads_add_ons_page');
        }
    }
}

add_action( 'admin_menu', 'quads_add_options_link', 10 );

/**
 *  Determines whether the current admin page is an QUADS add-on page.
 *
 *  Only works after the `wp_loaded` hook, & most effective
 *  starting on `admin_menu` hook.
 *
 *  @since 1.4.9
 *  @return bool True if QUADS admin page.
 */
function quads_is_addon_page() {
    // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Reason: We are not processing form information but only checking wheather current page is a  Quads add-on page.
    $currentpage = isset($_GET['page']) ? sanitize_text_field( wp_unslash( $_GET['page'] ) ) : '';
	if ( ! is_admin() || ! did_action( 'wp_loaded' ) ) {
		return false;
	}

	if ( 'quads-addons' == $currentpage ) {
		return true;
	}
}

function quads_has_setting_access(){
    global $quads_options;
    $user = wp_get_current_user();
    $roles = ( array ) $user->roles;
    $role_access = isset($quads_options['RoleBasedAccess'])?$quads_options['RoleBasedAccess']:[];
    foreach ($roles as $role) {
        if ($role == 'administrator' || $role == 'super_admin') {
            return true; // User has access
        }
        foreach ($role_access as $roleAccess) {
            if ($roleAccess['value'] === $role && $roleAccess['setting_access'] === true) {
                return true; // User has access
            }
        }
    }
    return false;
}