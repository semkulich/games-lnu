let WheelOfFortune = createReactClass({
  getInitialState: function () {
    return {
      spinning: false,
      hasSpun: false,
      selectedQuestion: null,
      answer: "",
      totalAngle: 0,
      questions: [],
      successMessage: "", // Додано для зберігання повідомлення про успіх
      showWheel: true // Додано для контролю видимості колеса
    };
  },

  componentDidMount: function () {
    // Завантаження питань із REST API при першому рендерингу компонента
    fetch(`/api/questions/${this.props.nodeId}`)
      .then(response => {
        if (!response.ok) {
          throw new Error("Network response was not ok");
        }
        return response.json();
      })
      .then(data => {
        this.setState({ questions: data.questions });
      })
      .catch(error => console.error("Error loading questions:", error));
  },

  spinWheel: function (event) {
    event.preventDefault();
    if (this.state.hasSpun || this.state.questions.length === 0) return;

    this.setState({ spinning: true, hasSpun: true });
    const randomIndex = Math.floor(Math.random() * this.state.questions.length);
    const anglePerSegment = 360 / this.state.questions.length;
    const baseSpin = 1440;
    const selectedAngle = randomIndex * anglePerSegment;
    const correctionAngle = 360 - selectedAngle;
    const finalAngle = baseSpin + correctionAngle;

    this.setState({ totalAngle: finalAngle });

    setTimeout(() => {
      const selectedQuestion = this.state.questions[randomIndex]; // Зберігаємо вибране питання
      this.setState({
        selectedQuestion: selectedQuestion, // Просто рядок
        spinning: false
      });
    }, 3000);
  },

  handleAnswerChange: function (event) {
    this.setState({ answer: event.target.value });
  },

  handleSubmitAnswer: function (event) {
    event.preventDefault();

    // Формуємо дані для надсилання
    const answerData = {
      question: this.state.selectedQuestion, // Просто зберігаємо рядок
      answer: this.state.answer
    };

    // Отримуємо CSRF токен
    let csrfToken = '';

    fetch('/session/token')
      .then(response => {
        if (!response.ok) {
          throw new Error('Не вдалося отримати CSRF токен');
        }
        return response.text();
      })
      .then(token => {
        csrfToken = token;
        // Надсилаємо відповідь на сервер
        fetch('/api/answers', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-Token': csrfToken
          },
          body: JSON.stringify(answerData),
        })
          .then(response => {
            if (!response.ok) {
              throw new Error('Network response was not ok');
            }
            return response.json();
          })
          .then(data => {
            this.setState({
              successMessage: "Ваша відповідь успішно збережена!", // Оновлюємо повідомлення про успіх
              answer: "",
              selectedQuestion: null,
              showWheel: false // Ховаємо колесо після успішної відправки
            });
          })
          .catch(error => console.error("Error submitting answer:", error));
      })
      .catch(error => console.error('Помилка при отриманні CSRF токена:', error));
  },

  render: function () {
    const { spinning, hasSpun, selectedQuestion, questions, totalAngle, successMessage, showWheel } = this.state;

    return (
      <div>
        <h1>Гра "Колесо фортуни"</h1>

        <div className="wheel-container">
          {successMessage && <p>{successMessage}</p>} {/* Відображаємо повідомлення про успіх */}

          {showWheel && ( // Відображаємо колесо лише якщо showWheel true
            <React.Fragment>
              <div className="arrow"></div>

              <div
                className="wheel"
                style={{
                  transform: `rotate(${totalAngle}deg)`,
                  transition: `transform 3s ease-out`
                }}
              >
                {questions.map((question, index) => (
                  <div key={index} className="wheel-segment">
                    {index + 1} {/* Відображаємо порядковий номер питання */}
                  </div>
                ))}
              </div>

              {!spinning && !hasSpun && (
                <button onClick={this.spinWheel}>Крутити колесо</button>
              )}
            </React.Fragment>
          )}
        </div>

        {selectedQuestion && (
          <div className="question-block">
            <h2>Ваше питання:</h2>
            <p>{selectedQuestion}</p> {/* Просто відображаємо рядок */}
            <form onSubmit={this.handleSubmitAnswer}>
              <textarea
                value={this.state.answer}
                onChange={this.handleAnswerChange}
                placeholder="Ваша відповідь"
                required
              />
              <br />
              <button type="submit">Відправити відповідь</button>
            </form>
          </div>
        )}
      </div>
    );
  }
});

// Отримуємо ID ноди з URL
const pathArray = window.location.pathname.split('/');
const nodeId = pathArray[pathArray.length - 1];

// Рендеримо компонент WheelOfFortune в елементі з ідентифікатором "react_wheel"
ReactDOM.render(<WheelOfFortune nodeId={nodeId} />, document.getElementById('react_wheel'));
