<?php
interface MiddlewareInterface
{
    public function handle(Request $request, callable $next);
}
