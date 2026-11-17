<?php
/**
 * Hook loader.
 *
 * @package Exopite_Simple_Options_Example
 */

namespace Exopite_Simple_Options_Example;

/**
 * Collects all actions, filters and shortcodes of the plugin and registers them in run().
 *
 * Pattern: Mediator. Why: hook wiring lives in one place instead of being
 * spread over constructors, so classes stay free of side effects.
 */
final class Exopite_Simple_Options_Example_Loader {

	/**
	 * Registered actions.
	 *
	 * @var array<int, array{hook: string, component: object, callback: string, priority: int, accepted_args: int}>
	 */
	private array $actions = array();

	/**
	 * Registered filters.
	 *
	 * @var array<int, array{hook: string, component: object, callback: string, priority: int, accepted_args: int}>
	 */
	private array $filters = array();

	/**
	 * Registered shortcodes.
	 *
	 * @var array<int, array{tag: string, component: object, callback: string}>
	 */
	private array $shortcodes = array();

	/**
	 * Adds an action.
	 *
	 * @param string $hook          Action name.
	 * @param object $component     Object that holds the callback.
	 * @param string $callback      Method name on $component.
	 * @param int    $priority      Priority.
	 * @param int    $accepted_args Number of arguments passed to the callback.
	 */
	public function add_action( string $hook, object $component, string $callback, int $priority = 10, int $accepted_args = 1 ): void {
		$this->actions[] = array(
			'hook'          => $hook,
			'component'     => $component,
			'callback'      => $callback,
			'priority'      => $priority,
			'accepted_args' => $accepted_args,
		);
	}

	/**
	 * Adds a filter.
	 *
	 * @param string $hook          Filter name.
	 * @param object $component     Object that holds the callback.
	 * @param string $callback      Method name on $component.
	 * @param int    $priority      Priority.
	 * @param int    $accepted_args Number of arguments passed to the callback.
	 */
	public function add_filter( string $hook, object $component, string $callback, int $priority = 10, int $accepted_args = 1 ): void {
		$this->filters[] = array(
			'hook'          => $hook,
			'component'     => $component,
			'callback'      => $callback,
			'priority'      => $priority,
			'accepted_args' => $accepted_args,
		);
	}

	/**
	 * Adds a shortcode. The callback must return the HTML, not echo it.
	 *
	 * @param string $tag       Shortcode tag.
	 * @param object $component Object that holds the callback.
	 * @param string $callback  Method name on $component.
	 */
	public function add_shortcode( string $tag, object $component, string $callback ): void {
		$this->shortcodes[] = array(
			'tag'       => $tag,
			'component' => $component,
			'callback'  => $callback,
		);
	}

	/**
	 * Registers everything with WordPress.
	 */
	public function run(): void {
		foreach ( $this->filters as $hook ) {
			add_filter( $hook['hook'], array( $hook['component'], $hook['callback'] ), $hook['priority'], $hook['accepted_args'] );
		}
		foreach ( $this->actions as $hook ) {
			add_action( $hook['hook'], array( $hook['component'], $hook['callback'] ), $hook['priority'], $hook['accepted_args'] );
		}
		foreach ( $this->shortcodes as $shortcode ) {
			add_shortcode( $shortcode['tag'], array( $shortcode['component'], $shortcode['callback'] ) );
		}
	}
}
