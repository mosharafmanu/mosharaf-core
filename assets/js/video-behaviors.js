/**
 * Video behaviors handler
 *
 * Handles different video play modes:
 * - hover: Play on hover, pause on leave
 * - autoplay: Custom controls (play/pause + mute/unmute)
 * - onclick-popup: Opens in modal (video-popup.js)
 */

(function() {
	'use strict';

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', initializeVideoBehaviors);
	} else {
		initializeVideoBehaviors();
	}

	function initializeVideoBehaviors() {
		const videos = document.querySelectorAll('video[data-behavior]');

		if (videos.length === 0) {
			return;
		}

		videos.forEach(function(video) {
			const behavior = video.getAttribute('data-behavior');

			switch (behavior) {
				case 'hover':
					initializeHoverBehavior(video);
					break;
				case 'autoplay':
					initializeAutoplayControls(video);
					break;
			}
		});
	}

	function initializeAutoplayControls(video) {
		const container = video.closest('.video-container');
		if (!container) {
			return;
		}

		// Flag to control playback for autoplay=false videos
		// Shared between pause-on-load handler and custom controls
		var userHasClickedPlay = false;

		// Check for pause-on-load first (autoplay=false)
		// Pause immediately to show first frame/poster without playing
		const pauseOnLoad = video.getAttribute('data-pause-on-load');
		if (pauseOnLoad === 'true') {
			// Pause immediately if video is already playing
			if (!video.paused) {
				video.pause();
				video.currentTime = 0;
			}

			// Listen for play event and pause if user hasn't clicked play yet
			video.addEventListener('play', function pauseHandler() {
				if (!userHasClickedPlay) {
					video.pause();
					video.currentTime = 0;
				}
			}, { once: false });

			// Listen for click on video element (browser's play button)
			video.addEventListener('click', function() {
				if (video.paused) {
					userHasClickedPlay = true; // Allow play event to proceed
					video.play();
				}
			});
		}

		// Check for autoplay-on-scroll first, before checking for controls
		// This ensures autoplay-on-scroll works even when controls are disabled
		const autoplayOnScroll = video.getAttribute('data-autoplay-on-scroll');
		if (autoplayOnScroll === 'true') {
			initializeAutoplayOnScroll(video);
		}

		// Initialize custom controls if they exist
		const controlsWrapper = container.querySelector('.video-autoplay-controls');
		if (!controlsWrapper) {
			// Controls not found - this is OK if autoplay-on-scroll is enabled
			// Only warn if neither controls nor autoplay-on-scroll are present
			if (!autoplayOnScroll) {
				console.warn('Video autoplay controls not found in container');
			}
			return;
		}

		const playPauseBtn = controlsWrapper.querySelector('.video-play-pause-btn');
		const muteBtn = controlsWrapper.querySelector('.video-mute-btn');

		if (!playPauseBtn || !muteBtn) {
			console.warn('Video control buttons not found');
			return;
		}

		// Get the desired muted state from data attribute
		const desiredMuted = video.getAttribute('data-desired-muted') === 'true';

		function updatePlayPauseButton() {
			if (video.paused) {
				playPauseBtn.classList.remove('playing');
			} else {
				playPauseBtn.classList.add('playing');
			}
		}

		function updateMuteButton() {
			if (video.muted) {
				muteBtn.classList.add('muted');
			} else {
				muteBtn.classList.remove('muted');
			}
		}

		playPauseBtn.addEventListener('click', function(e) {
			e.preventDefault();
			if (video.paused) {
				// Set flag to allow play event to proceed
				userHasClickedPlay = true;

				const playPromise = video.play();
				if (playPromise !== undefined) {
					playPromise.catch(function(error) {
						console.warn('Video play failed:', error);
					});
				}
			} else {
				video.pause();
			}
		});

		muteBtn.addEventListener('click', function(e) {
			e.preventDefault();
			video.muted = !video.muted;
			updateMuteButton();
		});

		// Handle autoplay with desired muted state
		// If video is autoplaying and desired muted is false, unmute after play starts
		video.addEventListener('play', function() {
			if (!desiredMuted && video.muted) {
				// Unmute the video after it starts playing
				video.muted = false;
			}
			updatePlayPauseButton();
		});

		video.addEventListener('pause', updatePlayPauseButton);
		video.addEventListener('volumechange', updateMuteButton);

		updatePlayPauseButton();
		updateMuteButton();
	}

	function initializeHoverBehavior(video) {
		// CUSTOMIZE: Change hover trigger element selector if needed
		let hoverTrigger = video.closest('.video-container');

		if (!hoverTrigger) {
			hoverTrigger = video.parentNode;
		}

		if (!hoverTrigger) {
			console.warn('Could not find hover trigger element for video');
			return;
		}

		hoverTrigger.addEventListener('mouseenter', function() {
			const playPromise = video.play();
			if (playPromise !== undefined) {
				playPromise.catch(function(error) {
					console.warn('Video play failed on hover:', error);
				});
			}
		});

		hoverTrigger.addEventListener('mouseleave', function() {
			video.pause();
			video.currentTime = 0;
		});
	}

	function initializeAutoplayOnScroll(video) {
		// Pause the video immediately if it starts autoplaying
		// The autoplay attribute is needed to load the first frame/poster
		// But we don't want it to play until user scrolls to it
		if (!video.paused) {
			video.pause();
			video.currentTime = 0;
		}

		// Also listen for play event and pause if not in viewport yet
		var hasEnteredViewport = false;
		video.addEventListener('play', function() {
			if (!hasEnteredViewport) {
				video.pause();
				video.currentTime = 0;
			}
		}, { once: false });

		if (!('IntersectionObserver' in window)) {
			console.warn('Intersection Observer not supported');
			hasEnteredViewport = true; // Allow play since we can't detect viewport
			const playPromise = video.play();
			if (playPromise !== undefined) {
				playPromise.catch(function(error) {
					console.warn('Video autoplay failed:', error);
					showLowPowerModeControls(video);
				});
			}
			return;
		}

		let isVideoPlaying = false;

		// CUSTOMIZE: Adjust threshold (0.0 to 1.0) to change when video starts playing
		// 0.5 = 50% of video must be visible
		const observer = new IntersectionObserver(function(entries) {
			entries.forEach(function(entry) {
				if (entry.isIntersecting) {
					hasEnteredViewport = true; // Allow play event to proceed
					if (!isVideoPlaying) {
						const playPromise = entry.target.play();
						if (playPromise !== undefined) {
							playPromise
								.then(function() {
									isVideoPlaying = true;
									hideLowPowerModeControls(entry.target);
								})
								.catch(function(error) {
									console.warn('Video autoplay on scroll failed:', error);
									isVideoPlaying = false;
									showLowPowerModeControls(entry.target);
								});
						}
					}
				} else {
					hasEnteredViewport = false; // Prevent play event when out of viewport
					if (isVideoPlaying && !entry.target.paused) {
						entry.target.pause();
						entry.target.currentTime = 0;
						isVideoPlaying = false;
					}
				} 
			});
		}, {
			threshold: 0.5,
			rootMargin: '0px 0px -50px 0px'
		});

		video.addEventListener('play', function() {
			isVideoPlaying = true;
		});

		video.addEventListener('pause', function() {
			isVideoPlaying = false;
		});

		video.addEventListener('ended', function() {
			isVideoPlaying = false;
		});

		observer.observe(video);
		video.autoplayObserver = observer;
	}

	function showLowPowerModeControls(video) {
		const container = video.closest('.video-container');
		if (!container) {
			return;
		}

		const overlay = container.querySelector('.video-low-power-overlay');
		if (overlay) {
			overlay.classList.add('show');

			// Add click handler to play button
			const playBtn = overlay.querySelector('.low-power-play-btn');
			if (playBtn) {
				playBtn.addEventListener('click', function(e) {
					e.stopPropagation();
					video.play().then(function() {
						overlay.classList.remove('show');
					}).catch(function(error) {
						console.warn('Manual play failed:', error);
					});
				}, { once: true });
			}
		}
	}

	function hideLowPowerModeControls(video) {
		const container = video.closest('.video-container');
		if (!container) {
			return;
		}

		const overlay = container.querySelector('.video-low-power-overlay');
		if (overlay) {
			overlay.classList.remove('show');
		}
	}

	// Global function to reinitialize video behaviors (useful for AJAX-loaded content)
	window.reinitializeVideoBehaviors = function() {
		initializeVideoBehaviors();
	};

})();

