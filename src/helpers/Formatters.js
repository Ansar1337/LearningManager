"use strict";

export function formatDate(date) {
    return date
        ? new Date(date)
            .toLocaleString("ru-RU", {year: "numeric", month: "long", day: "numeric"})
            .replace(" г.", "")
        : "";
}

export function formatDateShort(date) {
    return date
        ? new Date(date)
            .toLocaleString("ru-RU", {year: "numeric", month: "short", day: "numeric"})
            .replace(" г.", "")
        : "";
}

export function formatEstimate(estimate) {
    if (estimate) {
        let hours = Math.floor((estimate / (1000 * 60 * 60)));
        if (hours >= 10 && hours <= 20 || [5, 6, 7, 8, 9, 0].includes(hours % 10))
            hours += " часов";
        else if ([2, 3, 4].includes(hours % 10))
            hours += " часа";
        else
            hours += " час";
        return hours;
    }
}

export function formatLongEstimate(estimate) {
    if (estimate) {
        let hours = Math.floor((estimate / (1000 * 60 * 60)));
        let minutes = Math.floor((estimate % (1000 * 60 * 60)) / (1000 * 60));

        if (hours >= 10 && hours <= 20 || [5, 6, 7, 8, 9, 0].includes(hours % 10))
            hours += " часов";
        else if ([2, 3, 4].includes(hours % 10))
            hours += " часа";
        else
            hours += " час";

        if (minutes >= 10 && minutes <= 20 || [5, 6, 7, 8, 9, 0].includes(minutes % 10))
            minutes += " минут";
        else if ([2, 3, 4].includes(minutes % 10))
            minutes += " минуты";
        else
            minutes += " минута";

        return `${hours} ${minutes}`;
    }
}