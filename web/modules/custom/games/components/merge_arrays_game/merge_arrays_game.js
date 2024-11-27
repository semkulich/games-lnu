let MergeGame = createReactClass({
  getInitialState: function () {
    return {
      fruits1: ['Яблуко', 'Банан', 'Апельсин'],
      fruits2: ['Виноград', 'Ананас', 'Груша'],
      mergedFruits: [],
      basket: [],
      message: null,
      isCorrect: false
    };
  },

  handleDragStart: function (e, fruit) {
    e.dataTransfer.setData('fruit', fruit);
  },

  handleDragOver: function (e) {
    e.preventDefault();
  },

  handleDrop: function (e, targetContainer) {
    const fruit = e.dataTransfer.getData('fruit');
    if (targetContainer === 'fruits1') {
      this.setState({ fruits1: [...this.state.fruits1, fruit] });
    } else if (targetContainer === 'fruits2') {
      this.setState({ fruits2: [...this.state.fruits2, fruit] });
    } else if (targetContainer === 'basket') {
      this.setState({ basket: [...this.state.basket, fruit] });
    }
  },

  mergeFruits: function () {
    this.setState({ mergedFruits: [...this.state.fruits1, ...this.state.fruits2] });
  },

  checkAnswer: function () {
    const correctAnswer = ['Ананас', 'Апельсин', 'Банан', 'Виноград', 'Груша', 'Яблуко'];
    if (JSON.stringify(this.state.mergedFruits) === JSON.stringify(correctAnswer)) {
      this.setState({ isCorrect: true, message: 'Правильна відповідь!' });
    } else {
      this.setState({ isCorrect: false, message: 'Неправильна відповідь!' });
    }
  },

  render: function () {
    const { fruits1, fruits2, basket, message, isCorrect } = this.state;

    return (
      <div className="game-board">
        <p>Перетягуйте фрукти з одного контейнера в інший в алфавітному порядку, а потім натисніть "Об'єднати фрукти", щоб скомбінувати їх.</p>
        <p>Після об'єднання натисніть "Перевірити відповідь", щоб перевірити, чи відповідає ваш варіант правильному.</p>
        {message && <div className="message" style={{ color: isCorrect ? 'green' : 'red' }}>{message}</div>}
        <div className="game-board-inner">
          <div className="fruits-container" onDragOver={this.handleDragOver} onDrop={(e) => this.handleDrop(e, 'fruits1')}>
            {fruits1.map((fruit, index) => (
              <div key={index} draggable onDragStart={(e) => this.handleDragStart(e, fruit)}>
                {fruit}
              </div>
            ))}
          </div>
          <div className="fruits-container" onDragOver={this.handleDragOver} onDrop={(e) => this.handleDrop(e, 'fruits2')}>
            {fruits2.map((fruit, index) => (
              <div key={index} draggable onDragStart={(e) => this.handleDragStart(e, fruit)}>
                {fruit}
              </div>
            ))}
          </div>
          <div className="basket" onDragOver={this.handleDragOver} onDrop={(e) => this.handleDrop(e, 'basket')}>
            <h2>Кошик</h2>
            {basket.map((fruit, index) => (
              <div key={index} draggable onDragStart={(e) => this.handleDragStart(e, fruit)}>
                {fruit}
              </div>
            ))}
          </div>
          <div className="buttons">
            <button onClick={this.mergeFruits}>Об'єднати фрукти</button>
            <button onClick={this.checkAnswer}>Перевірити відповідь</button>
          </div>
        </div>
      </div>
    );
  }
});

// Отримуємо ID елементу, де буде рендериться компонент
const element = document.getElementById('react_merge_game');

// Рендеримо компонент
if (element) {
  ReactDOM.render(<MergeGame />, element);
}
