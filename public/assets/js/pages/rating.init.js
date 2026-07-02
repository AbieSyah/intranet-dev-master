document.querySelector("#basic-rater") && (basicRating = raterJs({
    starSize: 22,
    rating: 3,
    element: document.querySelector("#basic-rater"),
    rateCallback: function(e, t) {
        this.setRating(e), t()
    }
})), document.querySelector("#rater-step") && (starRatingStep = raterJs({
    starSize: 22,
    rating: 1.5,
    element: document.querySelector("#rater-step"),
    rateCallback: function(e, t) {
        this.setRating(e), t()
    }
}));
var basicRating, starRatingStep, starRatingmessage, starRatingunlimited, starRatinghover, starRatingreset, messageDataService = {
    rate: function(e) {
        return {
            then: function(e) {
                setTimeout(function() {
                    e(5 * Math.random())
                }, 1e3)
            }
        }
    }
};
document.querySelector("#rater-message") && (starRatingmessage = raterJs({
    isBusyText: "Rating in progress. Please wait...",
    starSize: 22,
    element: document.querySelector("#rater-message"),
    rateCallback: function(e, t) {
        starRatingmessage.setRating(e), messageDataService.rate().then(function(e) {
            starRatingmessage.setRating(e), t()
        })
    }
})), document.querySelector("#rater-unlimitedstar") && (starRatingunlimited = raterJs({
    max: 16,
    readOnly: !0,
    rating: 4.4,
    element: document.querySelector("#rater-unlimitedstar")
})), document.querySelector("#rater-onhover") && (starRatinghover = raterJs({
    starSize: 22,
    rating: 0,
    element: document.querySelector("#rater-onhover"),
    rateCallback: function(e, t) {
        this.setRating(e), t()
    },
    onHover: function(e, t) {
        document.querySelector(".ratingnum").textContent = e
    },
    onLeave: function(e, t) {
        document.querySelector(".ratingnum").textContent = t
    }
})), document.querySelector("#rater-onhover-dt2") && (starRatinghover = raterJs({
    starSize: 22,
    rating: 0,
    element: document.querySelector("#rater-onhover-dt2"),
    rateCallback: function(e, t) {
        this.setRating(e), t()
    },
    onHover: function(e, t) {
        document.querySelector(".ratingnum-dt2").textContent = e
    },
    onLeave: function(e, t) {
        document.querySelector(".ratingnum-dt2").textContent = t
    }
})), document.querySelector("#rater-onhover-dt3") && (starRatinghover = raterJs({
    starSize: 22,
    rating: 0,
    element: document.querySelector("#rater-onhover-dt3"),
    rateCallback: function(e, t) {
        this.setRating(e), t()
    },
    onHover: function(e, t) {
        document.querySelector(".ratingnum-dt3").textContent = e
    },
    onLeave: function(e, t) {
        document.querySelector(".ratingnum-dt3").textContent = t
    }
})), document.querySelector("#rater-onhover-dt4") && (starRatinghover = raterJs({
    starSize: 22,
    rating: 0,
    element: document.querySelector("#rater-onhover-dt4"),
    rateCallback: function(e, t) {
        this.setRating(e), t()
    },
    onHover: function(e, t) {
        document.querySelector(".ratingnum-dt4").textContent = e
    },
    onLeave: function(e, t) {
        document.querySelector(".ratingnum-dt4").textContent = t
    }
})), document.querySelector("#rater-onhover-dt5") && (starRatinghover = raterJs({
    starSize: 22,
    rating: 0,
    element: document.querySelector("#rater-onhover-dt5"),
    rateCallback: function(e, t) {
        this.setRating(e), t()
    },
    onHover: function(e, t) {
        document.querySelector(".ratingnum-dt5").textContent = e
    },
    onLeave: function(e, t) {
        document.querySelector(".ratingnum-dt5").textContent = t
    }
})), document.querySelector("#rater-onhover-fap") && (starRatinghover = raterJs({
    starSize: 22,
    rating: 0,
    element: document.querySelector("#rater-onhover-fap"),
    rateCallback: function(e, t) {
        this.setRating(e), t()
    },
    onHover: function(e, t) {
        document.querySelector(".ratingnum-fap").textContent = e
    },
    onLeave: function(e, t) {
        document.querySelector(".ratingnum-fap").textContent = t
    }
})), document.querySelector("#rater-onhover-fap2") && (starRatinghover = raterJs({
    starSize: 22,
    rating: 0,
    element: document.querySelector("#rater-onhover-fap2"),
    rateCallback: function(e, t) {
        this.setRating(e), t()
    },
    onHover: function(e, t) {
        document.querySelector(".ratingnum-fap2").textContent = e
    },
    onLeave: function(e, t) {
        document.querySelector(".ratingnum-fap2").textContent = t
    }
})), document.querySelector("#rater-onhover-fap3") && (starRatinghover = raterJs({
    starSize: 22,
    rating: 0,
    element: document.querySelector("#rater-onhover-fap3"),
    rateCallback: function(e, t) {
        this.setRating(e), t()
    },
    onHover: function(e, t) {
        document.querySelector(".ratingnum-fap3").textContent = e
    },
    onLeave: function(e, t) {
        document.querySelector(".ratingnum-fap3").textContent = t
    }
})), document.querySelector("#rater-onhover-fap4") && (starRatinghover = raterJs({
    starSize: 22,
    rating: 0,
    element: document.querySelector("#rater-onhover-fap4"),
    rateCallback: function(e, t) {
        this.setRating(e), t()
    },
    onHover: function(e, t) {
        document.querySelector(".ratingnum-fap4").textContent = e
    },
    onLeave: function(e, t) {
        document.querySelector(".ratingnum-fap4").textContent = t
    }
})), document.querySelector("#rater-onhover-et") && (starRatinghover = raterJs({
    starSize: 22,
    rating: 0,
    element: document.querySelector("#rater-onhover-et"),
    rateCallback: function(e, t) {
        this.setRating(e), t()
    },
    onHover: function(e, t) {
        document.querySelector(".ratingnum-et").textContent = e
    },
    onLeave: function(e, t) {
        document.querySelector(".ratingnum-et").textContent = t
    }
})), document.querySelector("#rater-onhover-et2") && (starRatinghover = raterJs({
    starSize: 22,
    rating: 0,
    element: document.querySelector("#rater-onhover-et2"),
    rateCallback: function(e, t) {
        this.setRating(e), t()
    },
    onHover: function(e, t) {
        document.querySelector(".ratingnum-et2").textContent = e
    },
    onLeave: function(e, t) {
        document.querySelector(".ratingnum-et2").textContent = t
    }
})), document.querySelector("#rater-onhover-et3") && (starRatinghover = raterJs({
    starSize: 22,
    rating: 0,
    element: document.querySelector("#rater-onhover-et3"),
    rateCallback: function(e, t) {
        this.setRating(e), t()
    },
    onHover: function(e, t) {
        document.querySelector(".ratingnum-et3").textContent = e
    },
    onLeave: function(e, t) {
        document.querySelector(".ratingnum-et3").textContent = t
    }
})), document.querySelector("#rater-onhover-et4") && (starRatinghover = raterJs({
    starSize: 22,
    rating: 0,
    element: document.querySelector("#rater-onhover-et4"),
    rateCallback: function(e, t) {
        this.setRating(e), t()
    },
    onHover: function(e, t) {
        document.querySelector(".ratingnum-et4").textContent = e
    },
    onLeave: function(e, t) {
        document.querySelector(".ratingnum-et4").textContent = t
    }
})), document.querySelector("#rater-onhover-et5") && (starRatinghover = raterJs({
    starSize: 22,
    rating: 0,
    element: document.querySelector("#rater-onhover-et5"),
    rateCallback: function(e, t) {
        this.setRating(e), t()
    },
    onHover: function(e, t) {
        document.querySelector(".ratingnum-et5").textContent = e
    },
    onLeave: function(e, t) {
        document.querySelector(".ratingnum-et5").textContent = t
    }
})), document.querySelector("#rater-onhover-et6") && (starRatinghover = raterJs({
    starSize: 22,
    rating: 0,
    element: document.querySelector("#rater-onhover-et6"),
    rateCallback: function(e, t) {
        this.setRating(e), t()
    },
    onHover: function(e, t) {
        document.querySelector(".ratingnum-et6").textContent = e
    },
    onLeave: function(e, t) {
        document.querySelector(".ratingnum-et6").textContent = t
    }
})), document.querySelector("#rater-onhover-et7") && (starRatinghover = raterJs({
    starSize: 22,
    rating: 0,
    element: document.querySelector("#rater-onhover-et7"),
    rateCallback: function(e, t) {
        this.setRating(e), t()
    },
    onHover: function(e, t) {
        document.querySelector(".ratingnum-et7").textContent = e
    },
    onLeave: function(e, t) {
        document.querySelector(".ratingnum-et7").textContent = t
    }
})), document.querySelector("#rater-onhover-et8") && (starRatinghover = raterJs({
    starSize: 22,
    rating: 0,
    element: document.querySelector("#rater-onhover-et8"),
    rateCallback: function(e, t) {
        this.setRating(e), t()
    },
    onHover: function(e, t) {
        document.querySelector(".ratingnum-et8").textContent = e
    },
    onLeave: function(e, t) {
        document.querySelector(".ratingnum-et8").textContent = t
    }
})), document.querySelector("#rater-onhover-et9") && (starRatinghover = raterJs({
    starSize: 22,
    rating: 0,
    element: document.querySelector("#rater-onhover-et9"),
    rateCallback: function(e, t) {
        this.setRating(e), t()
    },
    onHover: function(e, t) {
        document.querySelector(".ratingnum-et9").textContent = e
    },
    onLeave: function(e, t) {
        document.querySelector(".ratingnum-et9").textContent = t
    }
})), document.querySelector("#rater-onhover-et10") && (starRatinghover = raterJs({
    starSize: 22,
    rating: 0,
    element: document.querySelector("#rater-onhover-et10"),
    rateCallback: function(e, t) {
        this.setRating(e), t()
    },
    onHover: function(e, t) {
        document.querySelector(".ratingnum-et10").textContent = e
    },
    onLeave: function(e, t) {
        document.querySelector(".ratingnum-et10").textContent = t
    }
})), document.querySelector("#rater-onhover-et11") && (starRatinghover = raterJs({
    starSize: 22,
    rating: 0,
    element: document.querySelector("#rater-onhover-et11"),
    rateCallback: function(e, t) {
        this.setRating(e), t()
    },
    onHover: function(e, t) {
        document.querySelector(".ratingnum-et11").textContent = e
    },
    onLeave: function(e, t) {
        document.querySelector(".ratingnum-et11").textContent = t
    }
})), document.querySelector("#rater-onhover-et12") && (starRatinghover = raterJs({
    starSize: 22,
    rating: 0,
    element: document.querySelector("#rater-onhover-et12"),
    rateCallback: function(e, t) {
        this.setRating(e), t()
    },
    onHover: function(e, t) {
        document.querySelector(".ratingnum-et12").textContent = e
    },
    onLeave: function(e, t) {
        document.querySelector(".ratingnum-et12").textContent = t
    }
})),

document.querySelector("#raterreset") && (starRatingreset = raterJs({
    starSize: 22,
    rating: 2,
    element: document.querySelector("#raterreset"),
    rateCallback: function(e, t) {
        this.setRating(e), t()
    }
})), document.querySelector("#raterreset-button") && document.querySelector("#raterreset-button").addEventListener("click", function() {
    starRatingreset.clear()
}, !1);