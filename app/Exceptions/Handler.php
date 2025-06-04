<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });

        // Gestion des exceptions de type non trouvé
        $this->renderable(function (NotFoundHttpException $e, $request) {
            return $this->renderErrorView('404', $request, 404);
        });

        // Gestion des exceptions d'authentification
        $this->renderable(function (AuthenticationException $e, $request) {
            return $this->renderErrorView('401', $request, 401);
        });

        // Gestion des exceptions d'autorisation
        $this->renderable(function (AuthorizationException $e, $request) {
            return $this->renderErrorView('403', $request, 403);
        });

        // Gestion des exceptions de token invalide (CSRF)
        $this->renderable(function (TokenMismatchException $e, $request) {
            return $this->renderErrorView('419', $request, 419);
        });

        // Gestion des exceptions de throttle (trop de requêtes)
        $this->renderable(function (ThrottleRequestsException $e, $request) {
            return $this->renderErrorView('429', $request, 429);
        });

        // Gestion des exceptions de méthode non autorisée
        $this->renderable(function (MethodNotAllowedHttpException $e, $request) {
            return $this->renderErrorView('405', $request, 405);
        });        // Gestion des exceptions HTTP génériques
        $this->renderable(function (HttpException $e, $request) {
            $statusCode = $e->getStatusCode();
            return $this->renderErrorView($statusCode, $request, $statusCode);
        });
          // Gestion des exceptions de modèle non trouvé
        $this->renderable(function (ModelNotFoundException $e, $request) {
            return $this->renderErrorView('404', $request, 404);
        });

        // Gestion des exceptions de validation
        $this->renderable(function (\Illuminate\Validation\ValidationException $e, $request) {
            if (!$request->expectsJson()) {
                return $this->renderErrorView('422', $request, 422);
            }
            return null;
        });

        // Gestion des exceptions générales avec une page 500
        $this->renderable(function (Throwable $e, $request) {
            if ($this->shouldReport($e) && !$this->isHttpException($e)) {
                return $this->renderErrorView('500', $request, 500);
            }

            return null;
        });
    }

    /**     * Afficher la vue d'erreur appropriée
     *
     * @param string|int $view
     * @param \Illuminate\Http\Request $request
     * @param int $statusCode
     * @return \Illuminate\Http\Response
     */
    private function renderErrorView($view, $request, $statusCode)
    {
        if ($request->expectsJson()) {
            return response()->json(['message' => $this->getErrorMessage($statusCode)], $statusCode);
        }

        // Vérifier si la vue existe, sinon utiliser la vue générique
        $viewName = "errors.{$view}";
        if (!view()->exists($viewName)) {
            $viewName = "errors.error";
            $data = ['errorCode' => $statusCode, 'message' => $this->getErrorMessage($statusCode)];
        } else {
            $data = [
                'isAuthenticated' => auth()->check()
            ];
        }

        return response()->view($viewName, $data, $statusCode);
    }

    /**
     * Récupérer le message d'erreur en fonction du code
     *
     * @param int $statusCode
     * @return string
     */    private function getErrorMessage($statusCode)
    {
        return match ($statusCode) {
            401 => __('You are not authorized to access this page. Please log in.'),
            403 => __('You do not have permission to access this resource.'),
            404 => __('The page you are looking for does not exist or has been moved.'),
            405 => __('Method not allowed.'),
            419 => __('Your session has expired. Please refresh and try again.'),
            422 => __('The submitted data was invalid. Please check your input and try again.'),
            429 => __('Please slow down. You have sent too many requests in a short period of time.'),
            500 => __('Something went wrong on our servers. We have been notified and are working on the issue.'),
            503 => __('The service is temporarily unavailable. Please try again later.'),
            default => __('Something went wrong.')
        };
    }

    /**
     * Préparation des réponses pour le mode maintenance
     *
     * {@inheritdoc}
     */
    public function renderForMaintenance($request, $exception)
    {
        if ($request->expectsJson()) {
            return response()->json(['message' => __('The service is temporarily unavailable. Please try again later.')], 503);
        }

        return $this->renderErrorView('503', $request, 503);
    }
}
