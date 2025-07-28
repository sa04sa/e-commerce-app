import React, { useState, useEffect } from 'react';

// Hook personnalisé pour les notifications
export const useNotifications = () => {
    const [notifications, setNotifications] = useState([]);

    const addNotification = (message, type = 'info', duration = 5000) => {
        const id = Date.now() + Math.random();
        const notification = {
            id,
            message,
            type, // success, error, warning, info
            duration,
            timestamp: Date.now()
        };

        setNotifications(prev => [...prev, notification]);

        // Auto-supprimer après la durée spécifiée
        if (duration > 0) {
            setTimeout(() => {
                removeNotification(id);
            }, duration);
        }

        return id;
    };

    const removeNotification = (id) => {
        setNotifications(prev => prev.filter(notif => notif.id !== id));
    };

    const clearAll = () => {
        setNotifications([]);
    };

    return {
        notifications,
        addNotification,
        removeNotification,
        clearAll
    };
};

// Composant Toast individuel
const Toast = ({ notification, onClose }) => {
    const [isVisible, setIsVisible] = useState(false);
    const [isExiting, setIsExiting] = useState(false);

    useEffect(() => {
        // Animation d'entrée
        setTimeout(() => setIsVisible(true), 10);
    }, []);

    const handleClose = () => {
        setIsExiting(true);
        setTimeout(() => {
            onClose(notification.id);
        }, 300);
    };

    const getToastClasses = () => {
        const baseClasses = 'toast-notification';
        const typeClasses = {
            success: 'toast-success',
            error: 'toast-error',
            warning: 'toast-warning',
            info: 'toast-info'
        };
        
        return `${baseClasses} ${typeClasses[notification.type] || typeClasses.info} ${
            isVisible && !isExiting ? 'toast-visible' : ''
        } ${isExiting ? 'toast-exiting' : ''}`;
    };

    const getIcon = () => {
        const icons = {
            success: 'fas fa-check-circle',
            error: 'fas fa-exclamation-circle',
            warning: 'fas fa-exclamation-triangle',
            info: 'fas fa-info-circle'
        };
        return icons[notification.type] || icons.info;
    };

    return (
        <div className={getToastClasses()}>
            <div className="toast-content">
                <div className="toast-icon">
                    <i className={getIcon()}></i>
                </div>
                <div className="toast-message">
                    {notification.message}
                </div>
                <button 
                    className="toast-close"
                    onClick={handleClose}
                    aria-label="Fermer"
                >
                    <i className="fas fa-times"></i>
                </button>
            </div>
            {notification.duration > 0 && (
                <div 
                    className="toast-progress"
                    style={{
                        animationDuration: `${notification.duration}ms`
                    }}
                ></div>
            )}
        </div>
    );
};

// Conteneur principal des notifications
const NotificationContainer = ({ notifications, onRemove }) => {
    return (
        <div className="notification-container">
            {notifications.map(notification => (
                <Toast
                    key={notification.id}
                    notification={notification}
                    onClose={onRemove}
                />
            ))}
        </div>
    );
};

// Composant principal du système de notifications
const NotificationSystem = () => {
    const { notifications, addNotification, removeNotification, clearAll } = useNotifications();

    // Exposer les fonctions globalement pour utilisation dans d'autres composants
    useEffect(() => {
        window.showNotification = addNotification;
        window.clearNotifications = clearAll;
        
        return () => {
            delete window.showNotification;
            delete window.clearNotifications;
        };
    }, [addNotification, clearAll]);

    return (
        <>
            <NotificationContainer 
                notifications={notifications}
                onRemove={removeNotification}
            />
            
            {/* Styles CSS intégrés */}
            <style jsx>{`
                .notification-container {
                    position: fixed;
                    top: 20px;
                    right: 20px;
                    z-index: 9999;
                    max-width: 400px;
                    pointer-events: none;
                }

                .toast-notification {
                    background: white;
                    border-radius: 8px;
                    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
                    margin-bottom: 10px;
                    opacity: 0;
                    transform: translateX(100%);
                    transition: all 0.3s ease;
                    pointer-events: auto;
                    position: relative;
                    overflow: hidden;
                    min-width: 300px;
                    max-width: 400px;
                }

                .toast-notification.toast-visible {
                    opacity: 1;
                    transform: translateX(0);
                }

                .toast-notification.toast-exiting {
                    opacity: 0;
                    transform: translateX(100%);
                }

                .toast-content {
                    display: flex;
                    align-items: center;
                    padding: 16px;
                }

                .toast-icon {
                    margin-right: 12px;
                    font-size: 20px;
                }

                .toast-message {
                    flex: 1;
                    font-size: 14px;
                    line-height: 1.4;
                }

                .toast-close {
                    background: none;
                    border: none;
                    font-size: 16px;
                    cursor: pointer;
                    opacity: 0.6;
                    margin-left: 12px;
                    padding: 4px;
                    border-radius: 4px;
                    transition: all 0.2s ease;
                }

                .toast-close:hover {
                    opacity: 1;
                    background: rgba(0, 0, 0, 0.1);
                }

                .toast-progress {
                    position: absolute;
                    bottom: 0;
                    left: 0;
                    height: 3px;
                    background: rgba(255, 255, 255, 0.3);
                    animation: toast-progress linear;
                    transform-origin: left;
                }

                @keyframes toast-progress {
                    from { transform: scaleX(1); }
                    to { transform: scaleX(0); }
                }

                /* Types de notifications */
                .toast-success {
                    border-left: 4px solid #28a745;
                }
                .toast-success .toast-icon {
                    color: #28a745;
                }
                .toast-success .toast-progress {
                    background: #28a745;
                }

                .toast-error {
                    border-left: 4px solid #dc3545;
                }
                .toast-error .toast-icon {
                    color: #dc3545;
                }
                .toast-error .toast-progress {
                    background: #dc3545;
                }

                .toast-warning {
                    border-left: 4px solid #ffc107;
                }
                .toast-warning .toast-icon {
                    color: #ffc107;
                }
                .toast-warning .toast-progress {
                    background: #ffc107;
                }

                .toast-info {
                    border-left: 4px solid #17a2b8;
                }
                .toast-info .toast-icon {
                    color: #17a2b8;
                }
                .toast-info .toast-progress {
                    background: #17a2b8;
                }

                /* Responsive */
                @media (max-width: 768px) {
                    .notification-container {
                        top: 10px;
                        right: 10px;
                        left: 10px;
                        max-width: none;
                    }
                    
                    .toast-notification {
                        min-width: auto;
                        max-width: none;
                    }
                }
            `}</style>
        </>
    );
};

// Fonctions utilitaires pour utilisation facile
export const showSuccess = (message, duration = 5000) => {
    if (window.showNotification) {
        return window.showNotification(message, 'success', duration);
    }
};

export const showError = (message, duration = 7000) => {
    if (window.showNotification) {
        return window.showNotification(message, 'error', duration);
    }
};

export const showWarning = (message, duration = 6000) => {
    if (window.showNotification) {
        return window.showNotification(message, 'warning', duration);
    }
};

export const showInfo = (message, duration = 5000) => {
    if (window.showNotification) {
        return window.showNotification(message, 'info', duration);
    }
};

export default NotificationSystem;