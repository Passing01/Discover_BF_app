@extends('layouts.hotel-manager')

@section('content')

    <div class="actions">
        <a href="{{ route('agency.hotels.customers.index', $hotel) }}" 
           class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
            <svg class="-ml-1 mr-2 h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
            </svg>
            {{ __('hotel.customer.actions.back') }}
        </a>
        <button type="button" class="ml-3 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
            <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z" />
                <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z" />
            </svg>
            {{ __('hotel.customer.actions.send_message') }}
        </button>
    </div>

    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="px-4 py-5 sm:px-6 bg-gray-50">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div class="flex items-center">
                    <div class="h-16 w-16 rounded-full bg-indigo-100 flex items-center justify-center text-2xl font-bold text-indigo-600">
                        {{ substr($customer->first_name, 0, 1) }}{{ substr($customer->last_name, 0, 1) }}
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg leading-6 font-medium text-gray-900">
                            {{ $customer->full_name }}
                        </h3>
                        <p class="mt-1 max-w-2xl text-sm text-gray-500">
                            {{ __('hotel.customer.customer_since', ['date' => $customer->created_at->format('d/m/Y')]) }}
                        </p>
                    </div>
                </div>
                <div class="mt-4 sm:mt-0">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                        <svg class="-ml-1 mr-1.5 h-2 w-2 text-green-400" fill="currentColor" viewBox="0 0 8 8">
                            <circle cx="4" cy="4" r="3" />
                        </svg>
                        {{ __('hotel.customer.labels.loyal_customer') }}
                    </span>
                </div>
            </div>
        </div>

        <div class="border-t border-gray-200 px-4 py-5 sm:px-6">
            <dl class="grid grid-cols-1 gap-x-4 gap-y-8 sm:grid-cols-2">
                <div class="sm:col-span-1">
                    <dt class="text-sm font-medium text-gray-500">
                        {{ __('hotel.customer.contact.email') }}
                    </dt>
                    <dd class="mt-1 text-sm text-gray-900">
                        {{ $customer->email }}
                    </dd>
                </div>
                <div class="sm:col-span-1">
                    <dt class="text-sm font-medium text-gray-500">
                        {{ __('hotel.customer.contact.phone') }}
                    </dt>
                    <dd class="mt-1 text-sm text-gray-900">
                        {{ $customer->phone ?? __('hotel.customer.contact.not_provided') }}
                    </dd>
                </div>
                <div class="sm:col-span-1">
                    <dt class="text-sm font-medium text-gray-500">
                        {{ __('hotel.customer.contact.country') }}
                    </dt>
                    <dd class="mt-1 text-sm text-gray-900">
                        {{ $customer->country ?? __('hotel.customer.contact.not_provided') }}
                    </dd>
                </div>
                @if($customer->birthdate)
                <div class="sm:col-span-1">
                    <dt class="text-sm font-medium text-gray-500">
                        {{ __('hotel.customer.contact.birthdate') }}
                    </dt>
                    <dd class="mt-1 text-sm text-gray-900">
                        {{ $customer->birthdate->format('d/m/Y') }} ({{ $customer->age }} ans)
                    </dd>
                </div>
                @endif
            </dl>
        </div>
    </div>

    <!-- Statistiques du client -->
    <div class="mt-6 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
        <!-- Nombre de réservations -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="px-4 py-5 bg-white shadow rounded-lg overflow-hidden sm:p-6">
                <dt class="text-sm font-medium text-gray-500 truncate">
                    {{ __('hotel.customer.stats.bookings') }}
                </dt>
                <dd class="mt-1 text-3xl font-semibold text-gray-900">
                    {{ $stats['total_bookings'] }}
                </dd>
            </div>
        </div>

        <!-- Nuits totales -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="px-4 py-5 bg-white shadow rounded-lg overflow-hidden sm:p-6">
                <dt class="text-sm font-medium text-gray-500 truncate">
                    {{ __('hotel.customer.stats.nights') }}
                </dt>
                <dd class="mt-1 text-3xl font-semibold text-gray-900">
                    {{ $stats['total_nights'] }}
                </dd>
            </div>
        </div>

        <!-- Dépenses totales -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="px-4 py-5 bg-white shadow rounded-lg overflow-hidden sm:p-6">
                <dt class="text-sm font-medium text-gray-500 truncate">
                    {{ __('hotel.customer.stats.total_spent') }}
                </dt>
                <dd class="mt-1 text-3xl font-semibold text-gray-900">
                    {{ number_format($stats['total_spent'], 2, ',', ' ') }} €
                </dd>
            </div>
        </div>

        <!-- Note moyenne -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="px-4 py-5 bg-white shadow rounded-lg overflow-hidden sm:p-6">
                <dt class="text-sm font-medium text-gray-500 truncate">
                    {{ __('hotel.customer.stats.avg_rating') }}
                </dt>
                <dd class="mt-1 text-3xl font-semibold text-gray-900">
                    @if($stats['average_rating'])
                        {{ number_format($stats['average_rating'], 1, ',', ' ') }}/5
                    @else
                        N/A
                    @endif
                </dd>
            </div>
        </div>
    </div>

    <!-- Historique des réservations -->
    <div class="mt-6 bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="px-4 py-5 border-b border-gray-200 sm:px-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900">
                {{ __('hotel.customer.history.title') }}
            </h3>
            <p class="mt-1 text-sm text-gray-500">
                {{ __('hotel.customer.history.description') }}
            </p>
        </div>

        @if($bookings->count() > 0)
            <div class="bg-white overflow-hidden overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                {{ __('hotel.customer.history.columns.reference') }}
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                {{ __('hotel.customer.history.columns.room') }}
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                {{ __('hotel.customer.history.columns.dates') }}
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                {{ __('hotel.customer.history.columns.nights') }}
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                {{ __('hotel.customer.history.columns.amount') }}
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                {{ __('hotel.customer.history.columns.status') }}
                            </th>
                            <th scope="col" class="relative px-6 py-3">
                                <span class="sr-only">{{ __('hotel.customer.history.columns.actions') }}</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($bookings as $booking)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    #{{ $booking->reference }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $booking->room->name }}</div>
                                    <div class="text-sm text-gray-500">{{ $booking->room->roomType->name }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        {{ $booking->check_in->format('d/m/Y') }} - {{ $booking->check_out->format('d/m/Y') }}
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        {{ $booking->created_at->format('d/m/Y H:i') }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $booking->nights }} nuit(s)
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ number_format($booking->total_amount, 2, ',', ' ') }} €
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $statusClasses = [
                                            'confirmed' => 'bg-green-100 text-green-800',
                                            'pending' => 'bg-yellow-100 text-yellow-800',
                                            'cancelled' => 'bg-red-100 text-red-800',
                                            'completed' => 'bg-blue-100 text-blue-800',
                                        ][$booking->status] ?? 'bg-gray-100 text-gray-800';
                                    @endphp
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusClasses }}">
                                        {{ ucfirst($booking->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <a href="{{ route('hotel-manager.bookings.show', $booking) }}" class="text-indigo-600 hover:text-indigo-900">
                                        {{ __('hotel.customer.actions.view') }}
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($bookings->hasPages())
                <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                    {{ $bookings->links() }}
                </div>
            @endif
        @else
            <div class="px-4 py-5 sm:p-6 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">Aucune réservation</h3>
                <p class="mt-1 text-sm text-gray-500">Ce client n'a encore effectué aucune réservation dans votre hôtel.</p>
            </div>
        @endif
    </div>

    <!-- Notes et préférences -->
    <div class="mt-6 bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="px-4 py-5 border-b border-gray-200 sm:px-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900">
                Notes et préférences
            </h3>
            <p class="mt-1 text-sm text-gray-500">
                Informations supplémentaires sur ce client
            </p>
        </div>
        <div class="px-4 py-5 sm:p-6">
            <form>
                <div>
                    <label for="notes" class="block text-sm font-medium text-gray-700">
                        Notes internes
                    </label>
                    <div class="mt-1">
                        <textarea id="notes" name="notes" rows="3" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 mt-1 block w-full sm:text-sm border border-gray-300 rounded-md"></textarea>
                    </div>
                    <p class="mt-2 text-sm text-gray-500">
                        Ces notes sont privées et ne seront pas visibles par le client.
                    </p>
                </div>
                <div class="mt-5">
                    <label class="block text-sm font-medium text-gray-700">
                        Préférences connues
                    </label>
                    <div class="mt-2 space-y-2">
                        <div class="flex items-center">
                            <input id="pref-non-smoking" name="preferences[]" type="checkbox" class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
                            <label for="pref-non-smoking" class="ml-3 block text-sm font-medium text-gray-700">
                                Chambre non-fumeur
                            </label>
                        </div>
                        <div class="flex items-center">
                            <input id="pref-high-floor" name="preferences[]" type="checkbox" class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
                            <label for="pref-high-floor" class="ml-3 block text-sm font-medium text-gray-700">
                                Étage élevé
                            </label>
                        </div>
                        <div class="flex items-center">
                            <input id="pref-early-checkin" name="preferences[]" type="checkbox" class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
                            <label for="pref-early-checkin" class="ml-3 block text-sm font-medium text-gray-700">
                                Check-in tôt
                            </label>
                        </div>
                        <div class="flex items-center">
                            <input id="pref-late-checkout" name="preferences[]" type="checkbox" class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
                            <label for="pref-late-checkout" class="ml-3 block text-sm font-medium text-gray-700">
                                Check-out tardif
                            </label>
                        </div>
                    </div>
                </div>
                <div class="mt-5">
                    <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Enregistrer les notes
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
