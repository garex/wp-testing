<?php

interface WpTesting_Addon_IWordPressFacade extends WpTesting_WordPress_IPriority
{

    /**
     * Hooks a function on to a specific action.
     *
     * @since 1.2.0
     * @link http://codex.wordpress.org/Function_Reference/add_action
     *
     * @param string $tag The name of the action to which the $function is hooked.
     * @param callback $function The name of the function you wish to be called.
     * @param int $priority optional. Used to specify the order in which the functions associated with a particular action are executed (default: 10). Lower numbers correspond with earlier execution, and functions with the same priority are executed in the order in which they were added to the action.
     * @param int $functionArgsCount optional. The number of arguments the function accept (default 1).
     * @return WpTesting_Addon_IWordPressFacade
     */
    public function addAction($tag, $function, $priority = self::PRIORITY_DEFAULT, $functionArgsCount = 1);

    /**
     * Add a meta box to an edit form.
     *
     * @since 2.5.0
     *
     * @param string           $id            String for use in the 'id' attribute of tags.
     * @param string           $title         Title of the meta box.
     * @param callable         $function      Function that fills the box with the desired content.
     *                                        The function should echo its output.
     * @param string|WP_Screen $screen        Optional. The screen on which to show the box (like a post
     *                                        type, 'link', or 'comment'). Default is the current screen.
     * @param string           $context       Optional. The context within the screen where the boxes
     *                                        should display. Available contexts vary from screen to
     *                                        screen. Post edit screen contexts include 'normal', 'side',
     *                                        and 'advanced'. Comments screen contexts include 'normal'
     *                                        and 'side'. Menus meta boxes (accordion sections) all use
     *                                        the 'side' context. Global default is 'advanced'.
     * @param string           $priority      Optional. The priority within the context where the boxes
     *                                        should show ('high', 'low'). Default 'default'.
     * @param array            $functionArgs  Optional. Data that should be set as the $args property
     *                                        of the box array (which is the second parameter passed
     *                                        to your callback). Default null.
     * @return WpTesting_Addon_IWordPressFacade
     */
    public function addMetaBox($id, $title, $function, $screen = null, $context = 'advanced', $priority = 'default', $functionArgs = null);

    /**
     * Hooks a function or method to a specific filter action.
     *
     * @since 0.71
     *
     * @param string $tag The name of the action to which the $function is hooked.
     * @param callback $function The name of the function you wish to be called.
     * @param int $priority optional. Used to specify the order in which the functions associated with a particular action are executed (default: 10). Lower numbers correspond with earlier execution, and functions with the same priority are executed in the order in which they were added to the action.
     * @param int $functionArgsCount optional. The number of arguments the function accept (default 1).
     * @return WpTesting_WordPressFacade
     */
    public function addFilter($tag, $function, $priority = self::PRIORITY_DEFAULT, $functionArgsCount = 1);

    /**
     * Whether the current request is for a network or blog admin page
     *
     * Does not inform on whether the user is an admin! Use capability checks to
     * tell if the user should be accessing a section or not.
     *
     * @since 1.5.1
     *
     * @return bool True if inside WordPress administration pages.
     */
    public function isAdministrationPage();
}
